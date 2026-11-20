<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\PluginManager;

class ConfigProvider
{
    /**
     * @return array{
     *     dependencies: array{factories: array<class-string, class-string>, initializers: list<class-string>},
     *     dot_controller: array{plugin_manager: array<string, mixed>, event_listeners: array<array-key, mixed>},
     * }
     */
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

    /**
     * @return array{factories: array<class-string, class-string>, initializers: list<class-string>}
     */
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
