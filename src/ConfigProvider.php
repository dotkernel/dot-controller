<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace DotKernel\DotController;

use DotKernel\DotController\Factory\PluginManagerAwareInitializer;
use DotKernel\DotController\Factory\PluginManagerFactory;
use DotKernel\DotController\Plugin\PluginManager;

/**
 * Class ConfigProvider
 * @package DotKernel\DotController
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dk_controller' => [
                'plugin_manager' => []
            ],

            'dependencies' => [
                'factories' => [
                    PluginManager::class => PluginManagerFactory::class,
                ],

                'initializers' => [
                    PluginManagerAwareInitializer::class,
                ],
            ],
        ];
    }
}