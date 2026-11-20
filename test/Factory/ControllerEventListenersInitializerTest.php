<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Factory\ControllerEventListenersInitializer;
use DotTest\Controller\Fixture\InMemoryContainer;
use DotTest\Controller\Fixture\TestController;
use DotTest\Controller\Fixture\TestListener;
use PHPUnit\Framework\TestCase;
use stdClass;

class ControllerEventListenersInitializerTest extends TestCase
{
    private ControllerEventListenersInitializer $initializer;
    private TestController $controller;

    protected function setUp(): void
    {
        TestListener::reset();
        $this->initializer = new ControllerEventListenersInitializer();
        $this->controller  = new TestController();
    }

    public function testAttachesAListenerGivenAsClassName(): void
    {
        $this->initialize([TestController::class => [TestListener::class]]);

        $this->assertSame(['before:indexAction', 'after'], $this->dispatch());
    }

    public function testAttachesAListenerGivenWithTypeAndPriority(): void
    {
        $this->initialize([TestController::class => [['type' => TestListener::class, 'priority' => '10']]]);
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            static function (): void {
                TestListener::$log[] = 'low';
            },
            1
        );

        $this->assertSame(['before:indexAction', 'after', 'low'], $this->dispatch());
    }

    public function testAttachesAListenerFromTheContainer(): void
    {
        $container = new InMemoryContainer([
            'config'   => ['dot_controller' => ['event_listeners' => [TestController::class => ['listener']]]],
            'listener' => new TestListener(),
        ]);

        ($this->initializer)($container, $this->controller);

        $this->assertSame(['before:indexAction', 'after'], $this->dispatch());
    }

    public function testListenAllAttachesToEachController(): void
    {
        $this->initialize([ControllerEventListenerInterface::LISTEN_ALL => [TestListener::class]]);

        $this->assertSame(['before:indexAction', 'after'], $this->dispatch());
    }

    public function testIgnoresListenersOfOtherControllers(): void
    {
        $this->initialize([stdClass::class => [TestListener::class]]);

        $this->assertSame([], $this->dispatch());
    }

    public function testIgnoresInstancesThatAreNotControllers(): void
    {
        $container = new InMemoryContainer();

        ($this->initializer)($container, new stdClass());

        $this->assertSame([], $this->dispatch());
    }

    public function testConfigWithoutTheControllerKeyAttachesNothing(): void
    {
        ($this->initializer)(new InMemoryContainer(['config' => []]), $this->controller);
        ($this->initializer)(new InMemoryContainer(), $this->controller);

        $this->assertSame([], $this->dispatch());
    }

    public function testInvalidListenerListsAreSkipped(): void
    {
        ($this->initializer)(
            new InMemoryContainer(['config' => ['dot_controller' => ['event_listeners' => 'invalid']]]),
            $this->controller
        );
        $this->initialize([TestController::class => 'invalid']);

        $this->assertSame([], $this->dispatch());
    }

    public function testEntryWithoutAValidTypeThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('must name a listener class or service, int given');

        $this->initialize([TestController::class => [['type' => 5]]]);
    }

    public function testEntryWithAnInvalidPriorityThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('must be an integer, array given');

        $this->initialize([TestController::class => [['type' => TestListener::class, 'priority' => []]]]);
    }

    public function testUnknownListenerThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('but string was provided');

        $this->initialize([TestController::class => ['UnknownListener']]);
    }

    public function testListenerOfTheWrongTypeThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('but stdClass was provided');

        $this->initialize([TestController::class => [stdClass::class]]);
    }

    /**
     * @param array<array-key, mixed> $listeners
     */
    private function initialize(array $listeners): void
    {
        $container = new InMemoryContainer([
            'config' => ['dot_controller' => ['event_listeners' => $listeners]],
        ]);

        $this->initializer->attachControllerListeners($container, $this->controller);
    }

    /**
     * @return list<string>
     */
    private function dispatch(): array
    {
        $before = $this->controller->dispatchEvent(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            ['method' => 'indexAction']
        );
        $this->assertInstanceOf(ControllerEvent::class, $before);
        $this->controller->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH);

        return TestListener::$log;
    }
}
