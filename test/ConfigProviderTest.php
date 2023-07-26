<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\ConfigProvider;
use Dot\Controller\Factory\ControllerEventListenersInitializer;
use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Plugin\PluginManager;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
        $this->assertArrayHasKey('dot_controller', $this->config);
        $this->assertArrayHasKey('plugin_manager', $this->config['dot_controller']);
        $this->assertArrayHasKey('event_listeners', $this->config['dot_controller']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(PluginManager::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey('initializers', $this->config['dependencies']);
        $this->assertContainsEquals(
            PluginManagerAwareInitializer::class,
            $this->config['dependencies']['initializers']
        );
        $this->assertContainsEquals(
            ControllerEventListenersInitializer::class,
            $this->config['dependencies']['initializers']
        );
    }
}
