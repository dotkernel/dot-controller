<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:31 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class ActionControllerEventListenerAwareTrait
 * @package Dot\Controller\Event
 */
trait ControllerEventListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var ControllerEventListenerInterface[] */
    protected $listeners = [];

    /**
     * @param ControllerEventListenerInterface $listener
     * @param int $priority
     */
    public function attachListener(ControllerEventListenerInterface $listener, $priority = 1)
    {
        $listener->attach($this->getEventManager(), $priority);
        $this->listeners[] = $listener;
    }

    /**
     * @param ControllerEventListenerInterface $listener
     */
    public function detachListener(ControllerEventListenerInterface $listener)
    {
        $listener->detach($this->getEventManager());
        $idx = 0;
        foreach ($this->listeners as $l) {
            if ($l === $listener) {
                break;
            }
            $idx++;
        }
        unset($this->listeners[$idx]);
    }

    /**
     * Detach listeners and clear them
     */
    public function clearListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }
        $this->listeners = [];
    }
}
