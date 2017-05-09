<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller;

use Dot\Controller\Event\DispatchControllerEventsTrait;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class AbstractController
 * @package Dot\Controller
 */
abstract class AbstractController implements
    MiddlewareInterface,
    PluginManagerAwareInterface,
    EventManagerAwareInterface
{
    use DispatchControllerEventsTrait;

    /** @var  PluginManager */
    protected $pluginManager;

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  DelegateInterface */
    protected $delegate;

    /** @var bool */
    protected $debug = false;

    /**
     * Transform an "action" token into a method name
     *
     * @param  string $action
     * @return string
     */
    public static function getMethodFromAction(string $action): string
    {
        $method = str_replace(['.', '-', '_'], ' ', $action);
        $method = ucwords($method);
        $method = str_replace(' ', '', $method);
        $method = lcfirst($method);
        $method .= 'Action';
        return $method;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $this->request = $request;
        $this->delegate = $delegate;

        return $this->dispatch();
    }

    abstract public function dispatch(): ResponseInterface;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return DelegateInterface
     */
    public function getDelegate(): DelegateInterface
    {
        return $this->delegate;
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call(string $method, array $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }
        return $plugin;
    }

    /**
     * Get plugin instance
     *
     * @param  string $name Name of plugin to return
     * @param  array $options Options to pass to plugin constructor (if not already instantiated)
     * @return PluginInterface|callable
     */
    public function plugin(string $name, array $options = []): PluginInterface
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager
    {
        if (!$this->pluginManager) {
            throw new RuntimeException('Controller plugin manager not set. Enable the controller module by merging ' .
                'its ConfigProvider and make sure the controller is registered in the service manager');
        }
        return $this->pluginManager;
    }

    /**
     * @param PluginManager $pluginManager
     */
    public function setPluginManager(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }
}
