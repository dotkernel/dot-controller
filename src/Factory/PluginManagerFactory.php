<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
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