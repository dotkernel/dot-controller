<?php
/**
 * @copyright: DotKernel
 * @package: dot-controller
 * @author: n3vrax
 * Date: 8/15/2016
 * Time: 5:56 PM
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