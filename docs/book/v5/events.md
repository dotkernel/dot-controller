# Events

Dotkernel's controller package supports events of two types: global events (like middleware) and events that you trigger manually.

## Getting started

- Each event listener for a controller must implement `Dot\Controller\Event\ControllerEventListenerInterface`, which extends `Laminas\EventManager\ListenerAggregateInterface`.
- Use the trait `Dot\Controller\Event\ControllerEventListenerTrait` to get default implementations of the interface methods. You can also extend `Dot\Controller\Event\AbstractControllerEventListener`.
- Register each event listener under the `['dot_controller']['event_listeners']` key. Each key is the class name of a controller, and each value is a list of listeners for that controller.
- The key `Dot\Controller\Event\ControllerEventListenerInterface::LISTEN_ALL` attaches the listeners to all controllers.

Each item in the list of listeners can be one of these:

- the class name or the service name of a listener
- an array with the keys `type` (the class name or the service name) and `priority` (an integer, `1` by default)

The package gets the listener from the container when the container has it.
Otherwise, it creates the listener with `new`.

## Usage

You can use events in a controller in two ways:

- the global way: the package attaches the events automatically to all the actions of the controller, and they work like middleware
- the manual way: you attach the events to a controller, and you trigger them where you want

The examples use this `UserController`:

```php
use Dot\Controller\AbstractActionController;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractActionController
{
    public function indexAction(): ResponseInterface
    {
        return new HtmlResponse('User index');
    }

    public function registerAction(): ResponseInterface
    {
        return new HtmlResponse('Register');
    }

    // POST method that updates the user
    public function updateAction(): ResponseInterface
    {
        return new HtmlResponse('Updated');
    }
}
```

### Global method

First, create the event listener:

```php
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Event\ControllerEventListenerTrait;
use Psr\Log\LoggerInterface;

class UserUpdatedListener implements ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function onBeforeDispatch(ControllerEvent $e): void
    {
        $this->logger->info('on before dispatch');
    }

    public function onAfterDispatch(ControllerEvent $e): void
    {
        $this->logger->info('on after dispatch');
    }
}
```

The listener needs a logger, so register a factory for it in the container.
Then register the event listener in the configuration key:

```php
'dot_controller' => [
    'event_listeners' => [
        UserController::class => [
            UserUpdatedListener::class,
        ],
    ],
],
```

The package triggers `onBeforeDispatch` right before the action runs, and `onAfterDispatch` right after it.
Thus, for any action, the listener runs before and after the action.
Use the `$e` variable to get information about the event.

The event has these parameters:

| Parameter | Before dispatch | After dispatch |
| --- | --- | --- |
| `request` | The current request | The request before the action |
| `handler` | The next request handler | The same |
| `controller` | The controller | The same |
| `method` | The name of the action method, for example `updateAction` | The same |
| `response` | Not set | The response of the action |

For example:

```php
// UserUpdatedListener
public function onAfterDispatch(ControllerEvent $e): void
{
    $method = $e->getParam('request')->getMethod();
    $action = $e->getParam('method');
    if ($method === 'POST' && $action === 'updateAction') {
        $this->logger->info('The user was updated');
    }
}
```

Each time a POST request calls `updateAction`, the listener writes a log line right after the action.
Use `onBeforeDispatch` in the same way to write a log line right before the action.

### Replace the request

A listener of the "before dispatch" event can give the action a different request.
Set the `request` parameter of the event:

```php
public function onBeforeDispatch(ControllerEvent $e): void
{
    $request = $e->getParam('request');
    $e->setParam('request', $request->withAttribute('startedAt', time()));
}
```

### Stop the dispatch

A listener that returns a `Psr\Http\Message\ResponseInterface` stops the other listeners, and the controller returns that response.
A response from a "before dispatch" listener prevents the action.
A response from an "after dispatch" listener replaces the response of the action.

The methods `onBeforeDispatch()` and `onAfterDispatch()` return `void`.
To return a response, attach your own method in `attach()`:

```php
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Event\ControllerEventListenerTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\EventManager\EventManagerInterface;
use Psr\Http\Message\ResponseInterface;

class LoginRequiredListener implements ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            [$this, 'checkLogin'],
            $priority
        );
    }

    public function checkLogin(ControllerEvent $e): ?ResponseInterface
    {
        $request = $e->getParam('request');
        if ($request->getAttribute('identity') === null) {
            return new RedirectResponse('/login');
        }

        return null;
    }
}
```

### Manual method

```php
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Event\ControllerEventListenerTrait;
use Laminas\EventManager\EventManagerInterface;
use Psr\Log\LoggerInterface;

class UserProfileListener implements ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            'user.profile.update',
            [$this, 'userProfileUpdated'],
            $priority
        );
    }

    public function userProfileUpdated(ControllerEvent $e): void
    {
        $this->logger->info('User profile updated');
    }
}
```

The `attach()` method comes from `ListenerAggregateInterface`.
`ControllerEventListenerTrait` gives a default `attach()` that listens to the `onBeforeDispatch` and `onAfterDispatch` events.
This listener replaces it, and attaches a custom event, `user.profile.update`, to the method `userProfileUpdated`.

Next, register the listener:

```php
'dot_controller' => [
    'event_listeners' => [
        UserController::class => [
            UserProfileListener::class,
        ],
    ],
],
```

Now trigger the event from the controller with the built-in `dispatchEvent()` method:

```php
// UserController
// POST method that updates the user
public function updateAction(): ResponseInterface
{
    // update the user
    $this->dispatchEvent('user.profile.update', ['user' => $user]);

    return new HtmlResponse('Updated');
}
```

`dispatchEvent()` returns the event, or the response of a listener that returned one.
The code gives the `user` key to the parameters, so the listener can read it:

```php
public function userProfileUpdated(ControllerEvent $e): void
{
    $user = $e->getParam('user');
    $this->logger->info('User profile updated', $user->toArray());
}
```
