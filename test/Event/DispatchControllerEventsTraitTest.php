<?php

declare(strict_types=1);

namespace DotTest\Controller\Event;

use Dot\Controller\AbstractController;
use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\DispatchControllerEventsTrait as Subject;
use Dot\Controller\Exception\RuntimeException;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class DispatchControllerEventsTraitTest extends TestCase
{
    private AbstractController $subject;

    protected function setUp(): void
    {
        $this->subject = new class extends AbstractController {
            use Subject;

            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };
    }

    public function testInvalidControllerDispatch(): void
    {
        $eventName = 'test.event.name';

        $data = ['key' => 'value'];

        $invalidController = new class {
            use Subject;
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only controllers can dispatch controller events');

        $invalidController->dispatchEvent($eventName, ['data' => $data]);
    }

    public function testDispatchWithDefaultTarget(): void
    {
        $eventName = 'test.event.name';

        $data = ['key' => 'value'];

        $result = $this->subject->dispatchEvent($eventName, ['data' => $data]);

        $this->assertInstanceOf(ControllerEvent::class, $result);

        $this->assertSame($eventName, $result->getName());
        $this->assertSame($this->subject, $result->getTarget());
        $this->assertSame($data, $result->getParam('data'));
    }

    public function testDispatchEventWithExplicitTarget(): void
    {
        $eventName = 'test.event.name';

        $data = ['key' => 'value'];

        $targetObject = new stdClass();

        $result = $this->subject->dispatchEvent($eventName, ['data' => $data], $targetObject);

        $this->assertInstanceOf(ControllerEvent::class, $result);

        $this->assertSame($eventName, $result->getName());
        $this->assertSame($targetObject, $result->getTarget());
        $this->assertSame($data, $result->getParam('data'));
    }
}
