<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use ArrayAccess;
use Laminas\EventManager\Event;

/**
 * @template TTarget of object|string|null
 * @template TParams of array|ArrayAccess|object
 * @extends Event<TTarget, TParams>
 */
class ControllerEvent extends Event
{
    public const EVENT_CONTROLLER_BEFORE_DISPATCH = 'event.controller.beforeDispatch';
    public const EVENT_CONTROLLER_AFTER_DISPATCH  = 'event.controller.afterDispatch';

    /**
     * @param TTarget $target
     * @param TParams $params
     */
    public function __construct(
        ?string $name = null,
        object|string|null $target = null,
        array|object $params = []
    ) {
        parent::__construct($name, $target, $params);
    }
}
