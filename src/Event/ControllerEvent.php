<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 4:20 PM
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

    /** @var  callable */
    protected $next;

    /**
     * @return callable
     */
    public function getNext(): callable
    {
        return $this->next;
    }

    /**
     * @param callable $next
     */
    public function setNext(callable $next)
    {
        $this->next = $next;
    }
}
