<?php

declare(strict_types=1);

namespace Dot\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function filter_var;
use function is_array;

use const FILTER_VALIDATE_BOOLEAN;

class PluginManagerAwareInitializer
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, object|null $instance = null): void
    {
        if ($instance instanceof PluginManagerAwareInterface) {
            $pluginManager = $container->get(PluginManager::class);
            if ($pluginManager instanceof PluginManager) {
                $instance->setPluginManager($pluginManager);
            }
        }

        // The debug flag is injected here too.
        if ($instance instanceof AbstractController) {
            $config = $container->has('config') ? $container->get('config') : [];
            $debug  = is_array($config) ? ($config['debug'] ?? false) : false;
            $instance->setDebug(filter_var($debug, FILTER_VALIDATE_BOOLEAN));
        }
    }
}
