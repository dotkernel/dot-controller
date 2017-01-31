<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vrax
 * Date: 1/27/2017
 * Time: 7:12 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

/**
 * Class AbstractControllerEventListener
 * @package Dot\Controller\Event
 */
abstract class AbstractControllerEventListener extends AbstractListenerAggregate implements
    ControllerEventListenerInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            ControllerEvent::EVENT_CONTROLLER_DISPATCH,
            [$this, 'onDispatch'],
            $priority
        );
    }

    public function onDispatch(ControllerEvent $e)
    {
        // NOOP: let classes implement it
    }
}
