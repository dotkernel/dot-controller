<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

trait ControllerEventListenerTrait
{
    use ListenerAggregateTrait;

    /**
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            [$this, 'onBeforeDispatch'],
            $priority
        );
        $this->listeners[] = $events->attach(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            [$this, 'onAfterDispatch'],
            $priority
        );
    }

    public function onBeforeDispatch(ControllerEvent $e): void
    {
    }

    public function onAfterDispatch(ControllerEvent $e): void
    {
    }
}
