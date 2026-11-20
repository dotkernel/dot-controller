<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\Exception\InvalidArgumentException;
use Dot\Controller\Factory\PluginManagerFactory;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use DotTest\Controller\Fixture\InMemoryContainer;
use DotTest\Controller\Fixture\StringPlugin;
use DotTest\Controller\Fixture\StringPluginFactory;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;

class PluginManagerFactoryTest extends TestCase
{
    public function testRegistersTheUrlPluginWhenTheHelperIsPresent(): void
    {
        $container = new InMemoryContainer([UrlHelper::class => $this->createStub(UrlHelper::class)]);

        $pluginManager = (new PluginManagerFactory())($container);

        $this->assertInstanceOf(UrlHelperPlugin::class, $pluginManager->get('url'));
        $this->assertFalse($pluginManager->has('template'));
    }

    public function testRegistersTheTemplatePluginWhenTheRendererIsPresent(): void
    {
        $container = new InMemoryContainer([
            TemplateRendererInterface::class => $this->createStub(TemplateRendererInterface::class),
        ]);

        $pluginManager = (new PluginManagerFactory())($container);

        $this->assertInstanceOf(TemplatePlugin::class, $pluginManager->get('template'));
        $this->assertFalse($pluginManager->has('url'));
    }

    public function testRegistersNoBuiltInPluginWithoutTheServices(): void
    {
        $pluginManager = (new PluginManagerFactory())(new InMemoryContainer());

        $this->assertFalse($pluginManager->has('url'));
        $this->assertFalse($pluginManager->has('template'));
    }

    public function testRegistersThePluginsFromTheConfig(): void
    {
        $container = new InMemoryContainer([
            'config' => [
                'dot_controller' => [
                    'plugin_manager' => ['factories' => ['string' => StringPluginFactory::class]],
                ],
            ],
        ]);

        $pluginManager = (new PluginManagerFactory())($container);

        $this->assertInstanceOf(StringPlugin::class, $pluginManager->get('string'));
    }

    public function testInvalidPluginManagerConfigThrows(): void
    {
        $container = new InMemoryContainer([
            'config' => ['dot_controller' => ['plugin_manager' => 'invalid']],
        ]);

        $this->expectException(InvalidArgumentException::class);

        (new PluginManagerFactory())($container);
    }
}
