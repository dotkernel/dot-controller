<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:21 PM
 */

namespace Dot\Controller\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface ActionControllerEventListenerInterface
 * @package Dot\Controller\Event
 */
interface ControllerEventListenerInterface extends ListenerAggregateInterface
{
    const LISTEN_ALL = 1;

    /**
     * @param ControllerEvent $e
     * @return mixed
     */
    public function onDispatch(ControllerEvent $e);
}
