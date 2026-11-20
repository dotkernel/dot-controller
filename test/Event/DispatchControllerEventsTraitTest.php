<?php

declare(strict_types=1);

namespace DotTest\Controller\Event;

use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\DispatchControllerEventsTrait;
use Dot\Controller\Exception\RuntimeException;
use DotTest\Controller\Fixture\TestController;
use Laminas\Diactoros\Response\TextResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

class DispatchControllerEventsTraitTest extends TestCase
{
    private TestController $controller;

    protected function setUp(): void
    {
        $this->controller = new TestController();
    }

    public function testOnlyControllersCanDispatch(): void
    {
        $plainObject = new class {
            use DispatchControllerEventsTrait;
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only controllers can dispatch controller events');

        $plainObject->dispatchEvent('test.event.name');
    }

    public function testDispatchWithDefaultTarget(): void
    {
        $result = $this->controller->dispatchEvent('test.event.name', ['key' => 'value']);

        $this->assertInstanceOf(ControllerEvent::class, $result);
        $this->assertSame('test.event.name', $result->getName());
        $this->assertSame($this->controller, $result->getTarget());
        $this->assertSame('value', $result->getParam('key'));
    }

    public function testDispatchWithExplicitTarget(): void
    {
        $target = new stdClass();

        $result = $this->controller->dispatchEvent('test.event.name', [], $target);

        $this->assertInstanceOf(ControllerEvent::class, $result);
        $this->assertSame($target, $result->getTarget());
    }

    public function testListenerResponseIsReturnedAndStopsTheOtherListeners(): void
    {
        $response = new TextResponse('stop');
        $calls    = 0;
        $events   = $this->controller->getEventManager();
        $events->attach('test.event.name', static fn (): TextResponse => $response, 10);
        $events->attach(
            'test.event.name',
            static function () use (&$calls): void {
                $calls++;
            },
            1
        );

        $result = $this->controller->dispatchEvent('test.event.name');

        $this->assertSame($response, $result);
        $this->assertSame(0, $calls);
    }

    public function testListenerReturnValueThatIsNotAResponseReturnsTheEvent(): void
    {
        $this->controller->getEventManager()->attach('test.event.name', static fn (): string => 'value');

        $this->assertInstanceOf(ControllerEvent::class, $this->controller->dispatchEvent('test.event.name'));
    }
}
