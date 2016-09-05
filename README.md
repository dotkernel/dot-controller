# dot-controller

This package contains controller like middleware to be used inside a DotKernel or Expressive application. It provides base classes for action based controllers similar to ZF3 controller component. It is more lightweight though, but supports controller plugins.

## Installation

We use this in our web starter packages, so it will automatically be installed if you use them. For manual installation, just add the appropriate package to your `composer.json`

```bash
$ composer require dotkernel/dot-controller
```

For default module dependencies, you need to merge the ConfigProvider class output to your config. Again, if you are using the web starter packages(frontend, admin etc.), this is already handled.

By doing this, you "enable" the module to be available functionally for your project.

## Usage

Middleware controllers act as a handler for multiple routes. Some conventions were made:
- register controllers in the routes array just like any expressive middleware. The requirement is that you should define an `action` route parameter(possibly optional) anywhere inside the route(e.g `/user[/{action}]`)
- action parameter value is converted to a method name inside the controller. Underscore, dot and line characters are removed and the action name is converted to camel-case suffixed by the string `Action`. For example a route and action pair like `/user/forgot-password` will be converted to method `forgotPasswordAction` inside a `UserController` class(as defined in routes config)
- the default action value, if not present in the URI is `index`, so you should always define an `indexAction` within your controllers for displaying a default page or redirecting.

In order to create your action based controllers, you must extend the abstract class `DotKernel\DotController\AbstractActionController`

##### Example 1
Creating a UserController with default action and a register action. Will handle routes `/user` and `/user/register`

##### UserController.php
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

Then register this controller in the `routes` config(we assume you use FastRouter and ZF3 service manager)
##### routes.global.php
```php
'dependencies' => [
    //add UserController class as a dependency, in invokable or factories etc.
    //for example
    'factories' => [
        \Your\Namespace\UserContoller::class => \Your\Namespace\UserContollerFactory::class, 
    ]
],

'routes' => [
    [
        'name' => 'user',
        'path' => '/user[/{action}]',
        'middleware' => Your\Namespace\UserController::class,
    ]
],
```

### Multiple controllers for the same route

Use case: You have defined a controller inside some package, with default actions. You want to add actions that fall into the same controller name(or route name more exactly). You want to do this without extending the controller provided by the package. In this case you can do the following
- create your own controller, independent of the package's controller which adds or overwrites actions
- ZE lets you define an array of middleware for a route, so you can register this controller before the package's controller

##### Example
```php
'routes' => [
    [
        'name' => 'user',
        'path' => '/user[/{action}]',
        'middleware' => [Your\Namespace\UserController::class, Package\UserController::class],
    ]
]
```

Now when a request for this route comes in, your controller will run first. Dot Controllers are designed to ignore requests that cannot be matched to one of its methods, so if no action matches, it will call the next middleware, in our case, the second controller. 
If this is the last controller, and action does not match here, it will go to the default 404 Not found page. There is a simple middleware defined in dk-base that makes sure any request that does not match will be converted explicitly to a 404 error.

## Controller plugins

Controllers support controller plugins, much like controllers in a ZF3 application. The module comes packed with a few common plugins, but you can extend controller functionality with your own plugins too.

### Usage

Controller plugins must implement `DotKernel\DotController\Plugin\PluginInterface`. You can add them to the config file, at key `['dk_controller']['plugin_manager']`. The design pattern uses the `AbstractPluginManager` provided by ZF3 service manager component. So, registration of a plugin under the aforementioned config key looks the same as the declaration of regular dependencies, as `AbstractPluginManager` actually extends `ServiceManager`.

Once registered, a plugin can be directly accessed in any controller, by calling a method with the plugin's name(the service name or the key at which the plugin is registered inside the manager)

Controller plugins offer the advantage of globally accessible functionality in any controller without to manually inject dependencies. Plugins should be used for functions that are common to any controller. Do not clutter controller's code with unnecessary plugins.

##### Example
```php
//inside a controller
//assume we've already registered a plugin called testPlugin
$this->testPlugin(); //will return the TestPlugin class so you can call any public defined method on it
$this->testPlugin()->someMethod();
```

### Built-in plugins
Note: Each of these plugins requires the associated ZE packages to be installed and available in your project.
Although these are optional, if a package is missing, the controller will not have the associated functionality available

- `template` wraps TemplateInterface provided by ZE, to make template engine accessible to any controller
- `urlHelper` wraps the UrlHelper class provided by ZE helpers package. Used to generate URIs from routes

### Writing custom controller plugins

//@TODO: write documentation