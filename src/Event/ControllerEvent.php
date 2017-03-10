<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Dot\Event\Event;

/**
 * Class ActionControllerEvent
 * @package Dot\Controller\Event
 */
class ControllerEvent extends Event
{
    const EVENT_CONTROLLER_DISPATCH = 'event.controller.dispatch';
}
