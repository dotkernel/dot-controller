<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Dot\Event\Event;

class ControllerEvent extends Event
{
    public const EVENT_CONTROLLER_BEFORE_DISPATCH = 'event.controller.beforeDispatch';
    public const EVENT_CONTROLLER_AFTER_DISPATCH  = 'event.controller.afterDispatch';
}
