<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\PluginManager;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'   => $this->getDependenciesConfig(),
            'dot_controller' => [
                'plugin_manager'  => [],
                'event_listeners' => [],
            ],
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [
            'factories'    => [
                PluginManager::class => PluginManagerFactory::class,
            ],
            'initializers' => [
                PluginManagerAwareInitializer::class,
                ControllerEventListenersInitializer::class,
            ],
        ];
    }
}
