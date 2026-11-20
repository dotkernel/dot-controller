# Installation

Install `dot-controller` with this Composer command:

```shell
composer require dotkernel/dot-controller
```

Version 5 requires PHP 8.3, 8.4, 8.5 or 8.6.

The package works with any [PSR-11](https://www.php-fig.org/psr/psr-11/) container.
The package does not install a container.
Dotkernel and Mezzio applications usually use Laminas Service Manager.
If your application uses it, require it in your own `composer.json`:

```shell
composer require laminas/laminas-servicemanager
```

Upgrade from an older version with the [upgrade guide](upgrading.md).
