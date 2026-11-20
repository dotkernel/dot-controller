# Upgrading

This page lists the changes in version 5 that can break your code.
For each change, it gives the symptom and the fix.

## Upgrade from version 4

### Requirements

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 support stops | Composer cannot install version 5 | Use PHP 8.3, 8.4, 8.5 or 8.6 |
| The package does not require `laminas/laminas-servicemanager` | `Class "Laminas\ServiceManager\ServiceManager" not found` | Add `laminas/laminas-servicemanager` to the `composer.json` of your application |
| The package does not require `dotkernel/dot-event` | `Class "Dot\Event\..." not found` | Add `dotkernel/dot-event` to the `composer.json` of your application if your code uses it |

### Plugin manager

| Change | Symptom | Fix |
| --- | --- | --- |
| `PluginManager` no longer extends `Laminas\ServiceManager\AbstractPluginManager` | A call to `setService()`, `configure()`, `setInvokableClass()` or another Laminas method fails | Use `setFactory()`, `setAlias()`, `get()`, `has()` or `build()` |
| The plugin manager supports only the `factories` and `aliases` keys | `InvalidArgumentException` at startup | Move each `invokables` entry to `factories`. Remove `delegators`, `abstract_factories`, `initializers`, `shared`, `lazy_services` and `services` |
| The Laminas exceptions are gone | A `catch` of `ServiceNotFoundException` or `InvalidServiceException` does not catch | Catch `Psr\Container\NotFoundExceptionInterface` or `Psr\Container\ContainerExceptionInterface` |
| `PluginManager` has no template parameter | A `@var PluginManager<...>` annotation fails static analysis | Use `PluginManager` without a template parameter |

#### Replace an invokable plugin

Before:

```php
'dot_controller' => [
    'plugin_manager' => [
        'invokables' => [
            'string' => StringPlugin::class,
        ],
    ],
],
```

After:

```php
'dot_controller' => [
    'plugin_manager' => [
        'factories' => [
            'string' => static fn (): StringPlugin => new StringPlugin(),
        ],
    ],
],
```

Use a factory class if your configuration is cached, because a cached configuration cannot hold a closure.

#### Catch a missing plugin

Before:

```php
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

try {
    $this->plugin('string');
} catch (ServiceNotFoundException $exception) {
    // ...
}
```

After:

```php
use Psr\Container\NotFoundExceptionInterface;

try {
    $this->plugin('string');
} catch (NotFoundExceptionInterface $exception) {
    // ...
}
```

### Controllers and events

| Change | Symptom | Fix |
| --- | --- | --- |
| `ControllerEvent` extends `Laminas\EventManager\Event` | `$event instanceof Dot\Event\Event` is `false` | Check for `Dot\Controller\Event\ControllerEvent` or `Laminas\EventManager\EventInterface` |
| `dispatchEvent()` returns `ControllerEvent\|ResponseInterface` | A class that overrides `dispatchEvent()` causes a fatal error | Change the return type of the override |
| `getPluginManager()` returns `PluginManager`, not `?PluginManager` | A class that implements `PluginManagerAwareInterface` or overrides the method causes a fatal error | Change the return type to `PluginManager` |
| `plugin()` returns `PluginInterface` | An override with the old return type causes a fatal error | Change the return type to `PluginInterface` |
| A listener response now stops the dispatch | Before version 5, a listener that returned a response caused a `TypeError` | No change is necessary. Make sure that your listeners return a response only when they want to stop the dispatch |
| An `action` attribute that is not a string selects `index` | Before version 5, it caused a `TypeError` | No change is necessary |
| The action `0` no longer selects `index` | A request for `/route/0` goes to the next middleware | Add a route for this case if you need it |

### Plugins

| Change | Symptom | Fix |
| --- | --- | --- |
| `UrlHelperPlugin` takes a `?string` fragment identifier | `TypeError` when you give an integer | Give a string |
| `TemplatePlugin` returns the plugin for a `null` or empty template name | Before version 5, it caused a `TypeError` | No change is necessary |

### Configuration

| Change | Symptom | Fix |
| --- | --- | --- |
| A listener entry without a valid `type`, or with a priority that is not an integer, throws `RuntimeException` | Startup fails with a clear message | Fix the entry in `dot_controller.event_listeners` |
| The `debug` flag accepts `1`, `'1'`, `'true'`, `'on'` and `'yes'` | Before version 5, a value that was not a boolean caused a `TypeError` | No change is necessary |

## Upgrade from version 3

Version 3 is no longer supported.
Upgrade to version 4 first with the version 4 documentation, then follow the steps above.
