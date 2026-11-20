# Plugins

Dotkernel's controller supports plugins, much like controllers in Laminas applications.
The package has two built-in plugins, and you can add your own plugins.

## Usage

Each controller plugin must implement `Dot\Controller\Plugin\PluginInterface`.
Create a factory for the plugin and register it under the `['dot_controller']['plugin_manager']['factories']` key, with the plugin name as key.

After registration, any controller can use the plugin: call a method with the name of the plugin.
If the plugin is invokable (it has an `__invoke()` method), the call invokes it with your arguments.
Otherwise, the call returns the plugin.

Each call creates a new plugin instance.
To keep an instance, store it in a variable.

Plugins give all controllers a function without manual injection of dependencies.
Use plugins only for functions that many controllers need.
Do not add unnecessary plugins to the controllers.

### Example

First, create the plugin. For this example, a string helper:

```php
use Dot\Controller\Plugin\PluginInterface;

class StringPlugin implements PluginInterface
{
    // A method must be public to be available in a controller.
    public function toUpper(string $string): string
    {
        return strtoupper($string);
    }
}
```

Create a factory for the plugin.
The plugin manager calls the factory with the application container, the plugin name and the build options:

```php
use Psr\Container\ContainerInterface;

class StringPluginFactory
{
    public function __invoke(ContainerInterface $container): StringPlugin
    {
        return new StringPlugin();
    }
}
```

Register the factory under the `['dot_controller']['plugin_manager']['factories']` key:

```php
'dot_controller' => [
    'plugin_manager' => [
        'factories' => [
            'string' => StringPluginFactory::class,
        ],
    ],
],
```

Do not register the plugin factory in the `dependencies` key.
The plugin manager is a separate PSR-11 container, and it uses only its own configuration.

Use the plugin in a controller:

```php
// In a controller action
$this->string();                  // returns the StringPlugin instance
$this->string()->toUpper('test'); // returns 'TEST'
```

### Aliases

Give a plugin a second name with the `aliases` key:

```php
'dot_controller' => [
    'plugin_manager' => [
        'aliases' => [
            'str' => 'string',
        ],
    ],
],
```

### Errors

| Situation | Exception |
| --- | --- |
| No plugin has this name | `Dot\Controller\Exception\PluginNotFoundException`, a PSR-11 `NotFoundExceptionInterface` |
| The factory does not return a `PluginInterface` | `Dot\Controller\Exception\InvalidPluginException`, a PSR-11 `ContainerExceptionInterface` |
| The plugin manager configuration has an unsupported key | `Dot\Controller\Exception\InvalidArgumentException` |

## Built-in plugins

The package has two built-in plugins, `url` and `template`.
The plugin manager registers each one only when the application container has the service that it needs.

### `url`

The `url` plugin is a `Dot\Controller\Plugin\UrlHelperPlugin`.
It needs the `Mezzio\Helper\UrlHelper` service.
Call it with arguments to generate a URL, or without arguments to get the plugin:

```php
// In a controller action
$url = $this->url('account', ['action' => 'foo', 'hash' => 'bar']);

// The same URL in two steps
$plugin = $this->url();
$url    = $plugin->generate('account', ['action' => 'foo', 'hash' => 'bar']);
```

A `null` route name generates a URL for the route that matched the current request.

### `template`

The `template` plugin is a `Dot\Controller\Plugin\TemplatePlugin`.
It needs the `Mezzio\Template\TemplateRendererInterface` service.
Call it with a template name to render the template, or without arguments to get the plugin:

```php
use Laminas\Diactoros\Response\HtmlResponse;

// In a controller action
return new HtmlResponse(
    $this->template('page::home', [
        'foo' => 'bar',
    ])
);
```

The plugin also has the methods `render()` and `addDefaultParam()`:

```php
$this->template()->addDefaultParam('page::home', 'title', 'Home');
```
