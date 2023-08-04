<?php

declare(strict_types=1);

namespace DotTest\Controller\Event;

use Dot\Controller\Event\ControllerEvent;
use Dot\Controller\Event\ControllerEventListenerTrait as Subject;
use Laminas\EventManager\EventManagerInterface;
use PHPUnit\Framework\TestCase;

use function is_callable;

class ControllerEventListenerTraitTest extends TestCase
{
    private object $traitObject;

    protected function setUp(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $this->traitObject = new class ($eventManager) {
            use Subject;

            private EventManagerInterface $eventManager;

            public function __construct(EventManagerInterface $eventManager)
            {
                $this->eventManager = $eventManager;
            }

            public function getEventManager(): EventManagerInterface
            {
                return $this->eventManager;
            }
        };
    }

    public function testAttach(): void
    {
        $eventManager = $this->traitObject->getEventManager();

        $eventManager->expects($this->exactly(2))
            ->method('attach')
            ->willReturnMap([
                [
                    ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
                    $this->callback(function ($callback) {
                        return is_callable($callback)
                            && $callback[0] === $this->traitObject
                            && $callback[1] === 'onBeforeDispatch';
                    }),
                    1,
                ],
                [
                    ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
                    $this->callback(function ($callback) {
                        return is_callable($callback)
                            && $callback[0] === $this->traitObject
                            && $callback[1] === 'onAfterDispatch';
                    }),
                    1,
                ],
            ]);

        $this->traitObject->attach($eventManager);
    }
}
