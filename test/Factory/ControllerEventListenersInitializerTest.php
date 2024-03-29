<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Factory\ControllerEventListenersInitializer as Subject;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ControllerEventListenersInitializerTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    private AbstractController|MockObject $controller;
    private EventManager|MockObject $eventManager;
    private Subject $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->container    = $this->createMock(ContainerInterface::class);
        $this->controller   = $this->createMock(AbstractController::class);
        $this->eventManager = $this->createMock(EventManager::class);
        $this->subject      = new Subject();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAttachControllerListeners(): void
    {
        $config = [
            'dot_controller' => [
                'event_listeners' => [
                    AbstractController::class => [
                        [
                            'type'     => 'ListenerClass1',
                            'priority' => 10,
                        ],
                        'ListenerClass2',
                    ],
                ],
            ],
        ];
        $this->container->method('get')->with('config')->willReturn($config);

        $this->container->method('has')->willReturnMap([
            ['ListenerClass1', true],
            ['ListenerClass2', true],
        ]);

        $this->container->method('get')->willReturnMap([
            ['ListenerClass1', $this->createMock(ControllerEventListenerInterface::class)],
            ['ListenerClass2', $this->createMock(ControllerEventListenerInterface::class)],
        ]);

        $this->controller->method('getEventManager')->willReturn($this->eventManager);

        $this->eventManager->expects($this->any())->method('attach')
            ->with($this->isInstanceOf(ControllerEventListenerInterface::class), 10);
        $this->eventManager->expects($this->any())->method('attach')
            ->with($this->isInstanceOf(ControllerEventListenerInterface::class), 1);

        $this->subject->attachControllerListeners($this->container, $this->controller);

        $this->assertTrue(true);
    }
}
