# Usage

Middleware controllers handle more than one route.
The package uses these conventions:

- Register controllers in the routes just like any Mezzio middleware.
  Define an `action` route parameter (it can be optional) in the route, for example `/user[/{action}]`.
- The package converts the value of the `action` parameter to a method name of the controller.
  It removes the underscore, dot and hyphen characters, converts the name to camel case, and adds the suffix `Action`.
  For example, the route and action pair `/user/forgot-password` calls the method `forgotPasswordAction`.
- When the `action` parameter is missing, empty, or not a string, the default action is `index`.
  Always define an `indexAction` in your controllers, to show a default page or to redirect.

To create an action-based controller, extend the abstract class `Dot\Controller\AbstractActionController`.

## Example

This `UserController` has a default action and a register action.
It handles the routes `/user` and `/user/register`:

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
}
```

Register this controller as routed middleware in the file `RoutesDelegator.php`, just like regular middleware:

```php
// Example from a Dotkernel RoutesDelegator
$app->route(
    '/user[/{action}]',
    UserController::class,
    [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
    'user'
);
```

### Multiple controllers for the same route

**Use case:**
A package defines a controller with default actions.
You want to add actions to the same controller name (more exactly, the same route name).
You do not want to extend the controller of the package.
Do these steps:

- Create your own controller, independent of the controller of the package, with the new actions.
- Mezzio lets you define an array of middleware for a route. Register your controller before the controller of the package.

When a request for this route comes in, your controller runs first.
A Dotkernel controller ignores a request that does not match one of its methods.
It then calls the next middleware, in this case the second controller.
If the action does not match in the last controller either, the request goes to the default "404 Not Found" page (handled by `NotFoundHandler`).

## Plugins

- [Plugins](plugins.md)

## Events

- [Events](events.md)
