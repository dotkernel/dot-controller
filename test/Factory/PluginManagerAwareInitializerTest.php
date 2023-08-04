<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Factory\PluginManagerAwareInitializer as Subject;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\PluginManagerAwareInterface;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class PluginManagerAwareInitializerTest extends TestCase
{
    private Subject $subject;
    private ContainerInterface|MockObject $container;
    private PluginManager|MockObject $pluginManager;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->subject       = new Subject();
        $this->container     = $this->createMock(ContainerInterface::class);
        $this->pluginManager = $this->createMock(PluginManager::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeSetsDebugFlagWhenInstanceIsAbstractController(): void
    {
        $this->container->method('get')
            ->willReturnMap([
                [PluginManager::class, $this->pluginManager],
                ['config', ['debug' => true]],
            ]);

        $instance = new class extends AbstractController {
            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };

        $this->subject->__invoke($this->container, $instance);

        $this->assertTrue($instance->isDebug());
    }
}
