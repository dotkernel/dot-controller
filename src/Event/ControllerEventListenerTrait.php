<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
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
