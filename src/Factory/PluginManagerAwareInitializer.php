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
use DotKernel\DotController\Plugin\PluginManagerAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class PluginManagerAwareInitializer
 * @package DotKernel\DotController\Factory
 */
class PluginManagerAwareInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof PluginManagerAwareInterface) {
            $pluginManager = $container->get(PluginManager::class);
            $instance->setPluginManager($pluginManager);
        }
    }
}