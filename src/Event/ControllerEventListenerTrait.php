<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 9:39 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class ControllerEventListenerTrait
 * @package Dot\Controller\Event
 */
trait ControllerEventListenerTrait
{
    use ListenerAggregateTrait;

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
        //no-op
    }
}
