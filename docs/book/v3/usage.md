# Usage

Middleware controllers act as a handler for multiple routes.
Some conventions were made:

- register controllers in the `routes` array just like any Mezzio middleware.
  The requirement is that you should define an `action` route parameter (possibly optional) anywhere inside the route(e.g `/user[/{action}]`).
- action parameter value is converted to a method name inside the controller.
  Underscore, dot and line characters are removed and the action name is converted to a camel-case suffixed by the string `Action`.
  For example, a route and action pair like `/user/forgot-password` will be converted to method `forgotPasswordAction`.
- the default action value, if not present in the URI is `index`, so you should always define an `indexAction` within your controllers for displaying a default page or redirecting.

To create your action-based controllers, you must extend the abstract class `Dot\Controller\AbstractActionController`.

## Example

Creating a UserController with a default action and a register action.
It will handle routes `/user` and `/user/register`:

```php
use Dot\Controller\AbstractActionController;

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
}
```

Then register this controller as routed middleware in file `RoutesDelegator.php` just like regular middleware:

```php
//Example from a Dotkernel RoutesDelegator
$app->route(
    '/user[/{action}]',
    UserController::class,
    [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
    'user'
);
```

### Multiple controllers for the same route

**Use case:**
You have defined a controller inside some package, with default actions. You want to add actions that fall into the same controller name (or route name more exactly).
You want to do this without extending the controller provided by the package.
In this case you can do the following:

- create your own controller, independent of the package's controller, which adds more actions
- Mezzio lets you define an array of middleware for a route, so you can register this controller before the package's controller

Now when a request for this route comes in, your controller will run first.
Dotkernel controllers are designed to ignore requests that cannot be matched to one of its methods, so if no action matches, it will call the next middleware, in our case, the second controller.
If this is the last controller, and the action does not match here, it will go to the default 404 Not found page (handled by NotFoundDelegate).

## Plugins

- [Plugins](plugins.md)

## Events

- [Events](events.md)
