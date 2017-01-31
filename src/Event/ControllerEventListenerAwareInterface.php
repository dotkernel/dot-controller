<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:30 PM
 */

declare(strict_types=1);

namespace Dot\Controller\Event;

/**
 * Interface ActionControllerEventListenerAwareInterface
 * @package Dot\Controller\Event
 */
interface ControllerEventListenerAwareInterface
{
    /**
     * @param ControllerEventListenerInterface $listener
     * @param $priority
     */
    public function attachListener(ControllerEventListenerInterface $listener, $priority = 1);

    /**
     * @param ControllerEventListenerInterface $listener
     */
    public function detachListener(ControllerEventListenerInterface $listener);

    /**
     * Detach and clear all listeners
     */
    public function clearListeners();
}
