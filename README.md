# dot-controller

This is DotKernel's controller package that can be use like middleware inside DotKernel or Mezzio application.
It provides base classes for action based controllers similar to Laminas controller component. It is more lightweight though, but supports controller plugins and event listeners

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-controller)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-controller/3.4.3)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-controller)](https://github.com/dotkernel/dot-controller/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-controller)](https://github.com/dotkernel/dot-controller/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-controller)](https://github.com/dotkernel/dot-controller/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-controller)](https://github.com/dotkernel/dot-controller/blob/3.0/LICENSE.md)

[![Build Static](https://github.com/dotkernel/dot-controller/actions/workflows/static-analysis.yml/badge.svg?branch=3.0)](https://github.com/dotkernel/dot-controller/actions/workflows/static-analysis.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-controller/graph/badge.svg?token=VUBG5LM4CK)](https://codecov.io/gh/dotkernel/dot-controller)

[![SymfonyInsight](https://insight.symfony.com/projects/c4aac671-40d7-4590-b1fa-b3e46a1e3f43/big.svg)](https://insight.symfony.com/projects/c4aac671-40d7-4590-b1fa-b3e46a1e3f43)

## Installation

Install `dot-controller` by executing the following Composer command:

```bash
$ composer require dotkernel/dot-controller
```

## Usage

Middleware controllers act as a handler for multiple routes. Some conventions were made:

- register controllers in the routes array just like any mezzio middleware. The requirement is that you should define an `action` route parameter(possibly optional) anywhere inside the route(e.g `/user[/{action}]`)
- action parameter value is converted to a method name inside the controller. Underscore, dot and line characters are removed and the action name is converted to camel-case suffixed by the string `Action`. For example a route and action pair like `/user/forgot-password` will be converted to method `forgotPasswordAction`.
- the default action value, if not present in the URI is `index`, so you should always define an `indexAction` within your controllers for displaying a default page or redirecting.

In order to create your action based controllers, you must extend the abstract class `DotKernel\DotController\AbstractActionController`

### Example

Creating a UserController with default action and a register action. Will handle routes `/user` and `/user/register`

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
}
```

Then register this controller as a routed middleware in file `RoutesDelegator.php` just like a regular middleware.

```php
//Example from a DotKernel RoutesDelegator
$app->route(
    '/user[/{action}]',
    UserController::class,
    [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
    'user'
);
```

### Multiple controllers for the same route

Use case: You have defined a controller inside some package, with default actions. You want to add actions that fall into the same controller name(or route name more exactly). You want to do this without extending the controller provided by the package. In this case you can do the following

- create your own controller, independent of the package's controller which adds more actions
- Mezzio lets you define an array of middleware for a route, so you can register this controller before the package's controller

Now when a request for this route comes in, your controller will run first. DotKernel controllers are designed to ignore requests that cannot be matched to one of its methods, so if no action matches, it will call the next middleware, in our case, the second controller.
If this is the last controller, and action does not match here, it will go to the default 404 Not found page(handled by NotFoundDelegate)
