<?php
/**
 * @copyright: DotKernel
 * @package: dot-controller
 * @author: n3vrax
 * Date: 8/15/2016
 * Time: 5:56 PM
 */

namespace DotKernel\DotController\Factory;

use DotKernel\DotController\Plugin\PluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class PluginManagerFactory
 * @package DotKernel\DotController\Factory
 */
class PluginManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PluginManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $pluginManager = new PluginManager($container, $container->get('config')['dk_controller']['plugin_manager']);
        return $pluginManager;
    }
}