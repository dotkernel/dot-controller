<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Factory\PluginManagerAwareInitializer as Subject;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class PluginManagerAwareInitializerTest extends TestCase
{
    private Subject $subject;
    private ContainerInterface $container;
    private PluginManager $pluginManager;
    public function setUp(): void
    {
        $this->subject       = new Subject();
        $this->container     = $this->createMock(ContainerInterface::class);
        $this->pluginManager = $this->createMock(PluginManager::class);
    }

    public function testInvokeSetsPluginManagerWhenInstanceIsPluginManagerAwareInterface(): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with(PluginManager::class)
            ->willReturn($this->pluginManager);

        $instance = $this->createMock(PluginManagerAwareInterface::class);

        $instance->expects($this->once())
            ->method('setPluginManager')
            ->with($this->equalTo($this->pluginManager));

        $this->subject->__invoke($this->container, $instance);
    }

    public function testInvokeSetsDebugFlagWhenInstanceIsAbstractController(): void
    {
        $this->container->method('get')
            ->willReturnMap([
                [PluginManager::class, $this->pluginManager],
                ['config', ['debug' => true]],
            ]);

        $instance = $this->getMockBuilder(AbstractController::class)
            ->getMockForAbstractClass();

        $this->subject->__invoke($this->container, $instance);

        $this->assertTrue($instance->isDebug());
    }
}
