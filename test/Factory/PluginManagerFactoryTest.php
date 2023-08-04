<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\Factory\PluginManagerFactory as Subject;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PluginManagerFactoryTest extends TestCase
{
    private Subject $subject;
    private ContainerInterface|MockObject $container;
    private TemplateRendererInterface|MockObject $templateRenderer;
    private UrlHelper|MockObject $urlHelper;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->subject          = new Subject();
        $this->container        = $this->createMock(ContainerInterface::class);
        $this->templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $this->urlHelper        = $this->createMock(UrlHelper::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithUrlHelper(): void
    {
        $config = [
            'dot_controller' => [
                'plugin_manager' => [],
            ],
        ];

        $this->container->method('has')->willReturnMap([
            [UrlHelper::class, true],
            [TemplateRendererInterface::class, false],
        ]);

        $this->container->method('get')->willReturnMap([
            [TemplateRendererInterface::class, $this->templateRenderer],
            [UrlHelper::class, $this->urlHelper],
            ['config', $config],
        ]);

        $pluginManager = $this->subject->__invoke($this->container);

        $this->assertInstanceOf(PluginManager::class, $pluginManager);
        $this->assertTrue($pluginManager->has('url'));
        $this->assertFalse($pluginManager->has('template'));
        $this->assertInstanceOf(UrlHelperPlugin::class, $pluginManager->get('url'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeWithTemplateRenderer(): void
    {
        $config = [
            'dot_controller' => [
                'plugin_manager' => [],
            ],
        ];

        $this->container->method('has')->willReturnMap([
            [UrlHelper::class, false],
            [TemplateRendererInterface::class, true],
        ]);

        $this->container->method('get')->willReturnMap([
            [TemplateRendererInterface::class, $this->templateRenderer],
            [UrlHelper::class, $this->urlHelper],
            ['config', $config],
        ]);

        $pluginManager = $this->subject->__invoke($this->container);

        $this->assertInstanceOf(PluginManager::class, $pluginManager);
        $this->assertTrue($pluginManager->has('template'));
        $this->assertFalse($pluginManager->has('url'));
        $this->assertInstanceOf(TemplatePlugin::class, $pluginManager->get('template'));
    }
}
