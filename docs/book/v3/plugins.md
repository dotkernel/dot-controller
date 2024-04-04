# Plugins

DotKernel's controller support plugins, much like controllers in a Laminas Applications.
The package comes packed with a few built in plugins, but you can extend controller functionality with your own plugins.

## Usage

Any controller plugins must implement `Dot\Controller\Plugin\PluginInterface`.
You need to create a factory in addition to the plugin and register it
under the `['dot_controller']['plugin_manager']['factories']` with the plugin name.

Once registered, a plugin can be directly accessed in any controller,
by calling a method with the plugin's name (the service name or the key at which the plugin is registered inside the manager)

Controller plugins offer the advantage of globally accessible functionality
in any controller without to manually inject dependencies.
Plugins should be used for functions that are common to any controller.
Do not clutter controller's code with unnecessary plugins.

### Example

First we create our desired plugin, for our example a string helper

```php
class StringPlugin implements PluginInterface
{

    // any method inside the plugin needs to be public if you want to access it from a controller
    public function toUpper(string $string): string
    {
        return strtoupper($string);
    }
}
```

We create a factory for the plugin

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

Register the factory under the `['dot_controller']['plugin_manager']['factories']` key.

```php
'dot_controller' => [
    'plugin_manager' => [
        'factories' => [
            'string' => StringPluginFactory::class
        ]
    ]
]
```

You don't need to register the plugin factory to a regular dependencies in a configuration
because `AbstractPluginManager` actually extends `ServiceManager`

Access it in a controller.

```php
//inside a controller 
$this->string(); // will return the StringPlugin class, so you can call any public method from it
$this->string()->toUpper("test") // will return TEST
```

## Build-in plugins

The package comes in with 2 default plugins ``template`` and `url`. You can use
them in the same way as our example above.

- `url` - the plugin is an instance of `Mezzio\Helper\UrlHelper`

```php
    //in a controller action 
    /** @var UrlHelper $url */
    $url = $this->url();
    echo $url->generate('account', ['action' => 'foo', 'hash' => 'bar'])
```

- `template` - the plugin is an instance of `Mezzio\Template\TemplateRendererInterface`

```php
    // in a controller action
    return new HtmlResponse(
        $this->template->render('page::home', [
            'foo' => 'bar'
        ])
    );
```
