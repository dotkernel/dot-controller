# Configuration

After installation, the package can be used immediately but if you want to use all features of the package, like plugins and events you need to register the `ConfigProvider` in your project by adding the below line to your configuration aggregator (usually: `config/config.php`):

```php
\Dot\Controller\ConfigProvider::class,
```
