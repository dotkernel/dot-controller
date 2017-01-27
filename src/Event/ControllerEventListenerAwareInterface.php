<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:30 PM
 */

namespace Dot\Controller\Event;

/**
 * Interface ActionControllerEventListenerAwareInterface
 * @package Dot\Controller\Event
 */
interface ControllerEventListenerAwareInterface
{
    /**
     * @param ControllerEventListenerInterface $listener
     * @return mixed
     */
    public function attachListener(ControllerEventListenerInterface $listener);

    /**
     * @param ControllerEventListenerInterface $listener
     * @return mixed
     */
    public function detachListener(ControllerEventListenerInterface $listener);

    /**
     * @return mixed
     */
    public function clearListeners();
}
