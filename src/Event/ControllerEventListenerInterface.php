<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Laminas\EventManager\ListenerAggregateInterface;

interface ControllerEventListenerInterface extends ListenerAggregateInterface
{
    public const LISTEN_ALL = 1;

    /**
     * @param ControllerEvent<object|string|null, array<string, mixed>> $e
     */
    public function onBeforeDispatch(ControllerEvent $e): void;

    /**
     * @param ControllerEvent<object|string|null, array<string, mixed>> $e
     */
    public function onAfterDispatch(ControllerEvent $e): void;
}
