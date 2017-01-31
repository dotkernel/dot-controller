<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types = 1);

namespace Dot\Controller;

use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\PluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Dot\Controller
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'dot_controller' => [

                'plugin_manager' => [],

                'event_listeners' => [],
            ],
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [
            'factories' => [
                PluginManager::class => PluginManagerFactory::class,

                PluginManagerAwareInitializer::class => InvokableFactory::class,
                ControllerEventListenersInitializer::class => InvokableFactory::class,
            ],
        ];
    }
}
