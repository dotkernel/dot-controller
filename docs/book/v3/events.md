# Events

DotKernel's controller package supports events and those events can be of 2 types: global events (middleware-like) or manually dispatch  events.

## Getting started

- Every event listener that is triggered from a controller
needs to implement `Dot\Controller\Event\ControllerEventListenerInterface` which actually extends `Laminas\EventManager\ListenerAggregateInterface`.
- You can add the trait `Dot\Controller\Event\ControllerEventListenerTrait` to override the method of the interface.
- Every event listener needs to be registered under the `['dot_controller']['event_listenenrs]` key in the ConfigProvider, and every key must be the class that you want to attach the events

## Usage

The events in a controller can be done in 2 different ways, a global way where an event is attached automatically to all the controllers action and works in the same way the middlewares works
or a manually dispatchable way, where you can define to which controller the events is attached, and you can trigger the event where you want.

For our example we have a UserController with some methods in it

```php
use DotKernel\DotController\AbstractActionController;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
        //...
    }
    
    public function registerAction()
    {
        //...
    }
    
    // post method for updating the user
    public function updateAction()
    {
    
    }
}
```

### Example 1 - Global way

First we will create the event listener

```php
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Event\ControllerEventListenerTrait;

// for the logger we assume you will use your own logger and inject it

class UserUpdatedListener implements ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;
    
    public function onBeforeDispatch(ControllerEvent $event): void
    {
        $this->logger->info('on before dispatch');
    }

    public function onAfterDispatch(ControllerEvent $event): void
    {
        $this->logger->info('on after dispatch');
    }

}
```

We register the event listener in the configuration key

```php
'dot_controller' => [
    'event_listeners' => [
        AccountController::class => [
            UserUpdatedListener::class,
        ]
    ]
]
```

As you can assume, `onBeforeDispatch` is triggered right before the controller is dispatched, and `onAfterDispatch` right
after the controller is dispatched.

With this it doesn't matter what action is accessed, the event it will run before and after the action.

In addition, you can make use of the `event` variable to access information about the event.

For example:

```php
// UserUpdatedListener
public function onAfterDispatch(ControllerEvent $e): void
    {
        $method = $e->getTarget()->getRequest()->getMethod();
        $action = $e->getParams()['method'];
        if ($method == 'POST' && $action == 'updateAction') {
            $this->logger->info('this will trigger ');

        }
    }
```

So every time the `updateAction` is accessed and the method is post, 
right after the action is dispatched, we can log that the user was updated.

We can use the `onBeforeDispatch` in the same way, to log right before the user is updated.

### Example 2 - Manually triggered way

```php
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Event\ControllerEventListenerTrait;

// for the logger we assume you will use your own logger and inject it

class UserUpdatedListener implements ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;
    
     public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            'user.profile.update',
            [$this, 'userProfileUpdated'],
            $priority
        );
    }

    public function userProfileUpdated(ControllerEvent $event): void
    {
        $this->logger->info('User profile updated');
    }

}
```

The `attach` method is from the `ListenerAggregateInterface` which `ControllerEventListenerTrait` 
already is overriding it so can be used in a global way with `onBeforeDispatch` and `onAfterDispatch` 
methods, but we can make our custom event and bind it to our method.

In this case we create attach an event called `user.profile.update` and bind it to the `userProfileUpdated`  method.

Next we need to register the event

```php
'dot_controller' => [
    'event_listeners' => [
        AccountController::class => [
            'user.profile.update' => UserUpdatedListener::class
        ]
    ]
]
```

Now you can manually trigger the event from the controller using build in `dispatchEvent` method.

```php
// UserController
// post method for updating the user
public function updateAction()
{
    // logic
    $this->dispatchEvent('user.profile.update', ['user' => $user]);

}
```

As you can see we attach the `user` key to the parameters, so we can actually access it.

```php
    public function userProfileUpdated(ControllerEvent $event): void
    {
        $user = $event->getParams()['user'];
        $this->logger->info('User profile updated', $user->toArray());
    }
```
