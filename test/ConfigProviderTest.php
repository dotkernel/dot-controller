<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\ConfigProvider;
use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\PluginManager;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testConfigHasTheExpectedKeys(): void
    {
        $config = (new ConfigProvider())();

        $this->assertSame([], $config['dot_controller']['plugin_manager']);
        $this->assertSame([], $config['dot_controller']['event_listeners']);
        $this->assertSame(
            PluginManagerFactory::class,
            $config['dependencies']['factories'][PluginManager::class]
        );
        $this->assertSame(
            [PluginManagerAwareInitializer::class, ControllerEventListenersInitializer::class],
            $config['dependencies']['initializers']
        );
    }
}
