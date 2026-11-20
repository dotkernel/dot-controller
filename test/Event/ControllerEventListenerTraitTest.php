<?php

declare(strict_types=1);

namespace DotTest\Controller\Event;

use Dot\Controller\Event\ControllerEvent;
use DotTest\Controller\Fixture\TestListener;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\TestCase;

class ControllerEventListenerTraitTest extends TestCase
{
    protected function setUp(): void
    {
        TestListener::reset();
    }

    public function testAttachListensToBothControllerEvents(): void
    {
        $events = new EventManager();
        (new TestListener())->attach($events);

        $events->triggerEvent(new ControllerEvent(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            null,
            ['method' => 'indexAction']
        ));
        $events->triggerEvent(new ControllerEvent(ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH));

        $this->assertSame(['before:indexAction', 'after'], TestListener::$log);
    }

    public function testDetachRemovesTheListeners(): void
    {
        $events   = new EventManager();
        $listener = new TestListener();
        $listener->attach($events);
        $listener->detach($events);

        $events->triggerEvent(new ControllerEvent(ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH));

        $this->assertSame([], TestListener::$log);
    }

    public function testPriorityOrdersTheListeners(): void
    {
        $events = new EventManager();
        $events->attach(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            static function (): void {
                TestListener::$log[] = 'low';
            },
            1
        );
        (new TestListener())->attach($events, 10);

        $events->triggerEvent(new ControllerEvent(ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH));

        $this->assertSame(['after', 'low'], TestListener::$log);
    }
}
