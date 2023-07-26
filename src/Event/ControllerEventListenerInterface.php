<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Laminas\EventManager\ListenerAggregateInterface;

/**
 * Interface ActionControllerEventListenerInterface
 */
interface ControllerEventListenerInterface extends ListenerAggregateInterface
{
    public const LISTEN_ALL = 1;

    public function onBeforeDispatch(ControllerEvent $e);

    public function onAfterDispatch(ControllerEvent $e);
}
