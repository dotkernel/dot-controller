<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:31 PM
 */

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
     * @return $this
     */
    public function attachListener(ControllerEventListenerInterface $listener, $priority = 1)
    {
        $listener->attach($this->getEventManager(), $priority);
        $this->listeners[] = $listener;
        return $this;
    }

    /**
     * @param ControllerEventListenerInterface $listener
     * @return $this
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
        return $this;
    }

    /**
     * @return $this
     */
    public function clearListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }
        $this->listeners = [];
        return $this;
    }
}
