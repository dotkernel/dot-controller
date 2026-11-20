<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Event\DispatchControllerEventsTrait;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_callable;
use function lcfirst;
use function sprintf;
use function str_replace;
use function ucwords;

abstract class AbstractController implements
    MiddlewareInterface,
    PluginManagerAwareInterface,
    EventManagerAwareInterface
{
    use DispatchControllerEventsTrait;

    protected ?PluginManager $pluginManager = null;

    protected ServerRequestInterface $request;

    protected RequestHandlerInterface $handler;

    protected bool $debug = false;

    /**
     * Transform an "action" token into a method name
     */
    public static function getMethodFromAction(string $action): string
    {
        $method = str_replace(['.', '-', '_'], ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = lcfirst($method);
        return $method . 'Action';
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = $request;
        $this->handler = $handler;

        return $this->dispatch();
    }

    abstract public function dispatch(): ResponseInterface;

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Method overloading: return or call plugins
     *
     * If the plugin is invokable, call it with the given parameters.
     * Otherwise, return the plugin instance.
     *
     * @param array<array-key, mixed> $params
     * @throws ContainerExceptionInterface
     */
    public function __call(string $method, array $params): mixed
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return $plugin(...$params);
        }

        return $plugin;
    }

    /**
     * Create a new plugin instance on each call.
     *
     * @param array<string, mixed> $options
     * @throws ContainerExceptionInterface
     */
    public function plugin(string $name, array $options = []): PluginInterface
    {
        return $this->getPluginManager()->build($name, $options);
    }

    /**
     * @throws RuntimeException
     */
    public function getPluginManager(): PluginManager
    {
        if ($this->pluginManager === null) {
            throw new RuntimeException(
                sprintf(
                    'Controller plugin manager not set for controller `%s`.'
                    . ' Merge the ConfigProvider of dot-controller, register the controller in the container,'
                    . ' and make sure that the container runs the PluginManagerAwareInitializer',
                    static::class
                )
            );
        }

        return $this->pluginManager;
    }

    public function setPluginManager(PluginManager $plugins): void
    {
        $this->pluginManager = $plugins;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
}
