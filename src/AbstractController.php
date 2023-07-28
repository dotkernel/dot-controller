<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Event\DispatchControllerEventsTrait;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function call_user_func_array;
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
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     */
    public function __call(string $method, array $params): mixed
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }
        return $plugin;
    }

    /**
     * Get plugin instance
     */
    public function plugin(string $name, array $options = []): PluginInterface|callable
    {
        return $this->getPluginManager()->get($name, $options);
    }

    public function getPluginManager(): ?PluginManager
    {
        if (! $this->pluginManager) {
            throw new RuntimeException(
                sprintf(
                    'Controller plugin manager not set for controller `%s`.'
                    . ' Enable the controller module by merging'
                    . ' its ConfigProvider and make sure the controller is registered in the service manager',
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
