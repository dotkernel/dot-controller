<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 5:21 PM
 */

declare(strict_types=1);

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
     */
    public function onDispatch(ControllerEvent $e);
}
