<?php

declare(strict_types=1);

namespace Dot\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Psr\Container\ContainerInterface;

class PluginManagerAwareInitializer
{
    /**
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof PluginManagerAwareInterface) {
            $pluginManager = $container->get(PluginManager::class);
            $instance->setPluginManager($pluginManager);
        }

        // we'll inject the debug flag here too, for now
        if ($instance instanceof AbstractController) {
            $instance->setDebug($container->get('config')['debug'] ?? false);
        }
    }
}
