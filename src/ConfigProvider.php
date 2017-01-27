<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace Dot\Controller;

use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\PluginManager;

/**
 * Class ConfigProvider
 * @package Dot\Controller
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'dot_controller' => [

                'plugin_manager' => [],

                'event_listeners' => [],
            ],
        ];
    }

    public function getDependenciesConfig()
    {
        return [
            'factories' => [
                PluginManager::class => PluginManagerFactory::class,
            ],

            'initializers' => [
                PluginManagerAwareInitializer::class,
                ControllerEventListenersInitializer::class,
            ],
        ];
    }
}
