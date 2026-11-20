# Configuration

You can use the base controllers immediately after the installation.
To use plugins and events, add the `ConfigProvider` to your configuration aggregator (usually `config/config.php`):

```php
\Dot\Controller\ConfigProvider::class,
```

The `ConfigProvider` registers these items:

- a factory for `Dot\Controller\Plugin\PluginManager`
- the initializers `PluginManagerAwareInitializer` and `ControllerEventListenersInitializer`
- the `dot_controller` configuration key with empty defaults

## The `dot_controller` key

```php
'dot_controller' => [
    'plugin_manager'  => [
        'factories' => [
            // plugin name => factory class name or callable
        ],
        'aliases'   => [
            // alias => plugin name
        ],
    ],
    'event_listeners' => [
        // controller class name => list of listeners
    ],
],
```

The plugin manager supports only the `factories` and `aliases` keys.
Any other key, for example `invokables` or `delegators`, causes an `InvalidArgumentException`.

## Initializers and containers

The initializers give each controller the plugin manager, the `debug` flag and the event listeners.
Laminas Service Manager runs the initializers from the `dependencies.initializers` key.
Other PSR-11 containers ignore this key.
With such a container, call the two initializers in the factory of each controller:

```php
use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Psr\Container\ContainerInterface;

class UserControllerFactory
{
    public function __invoke(ContainerInterface $container): UserController
    {
        $controller = new UserController();
        (new PluginManagerAwareInitializer())($container, $controller);
        (new ControllerEventListenersInitializer())($container, $controller);

        return $controller;
    }
}
```

Do not use such a factory with Laminas Service Manager and the `ConfigProvider` together.
The listeners then attach two times.

## The `debug` flag

`PluginManagerAwareInitializer` reads the top-level `debug` configuration key and calls `setDebug()` on each controller.
The values `true`, `1`, `'1'`, `'true'`, `'on'` and `'yes'` enable it.
Any other value, or no value, disables it.
