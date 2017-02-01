<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types = 1);

namespace Dot\Controller;

use Dot\Controller\Event\ControllerEvent;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractActionController
 * @package Dot\Controller
 */
abstract class AbstractActionController extends AbstractController
{
    /**
     * @return ResponseInterface
     */
    public function dispatch(): ResponseInterface
    {
        $request = $this->request;

        $action = strtolower(trim($request->getAttribute('action', 'index')));
        if (empty($action)) {
            $action = 'index';
        }

        $action = AbstractController::getMethodFromAction($action);

        if (method_exists($this, $action)) {
            //trigger an event, and return if the result is a ResponseInterface
            $event = new ControllerEvent(ControllerEvent::EVENT_CONTROLLER_DISPATCH);
            $event->setTarget($this);
            $event->setParam('method', $action);
            $event->setRequest($this->getRequest());
            $event->setNext($this->getNext());

            $result = $this->getEventManager()->triggerEventUntil(function ($r) {
                return ($r instanceof ResponseInterface);
            }, $event)->last();

            if ($result instanceof ResponseInterface) {
                return $result;
            }

            return $this->$action();
        }

        //just go the the next middleware, it will eventually hit a 404 if no one handles the request
        $next = $this->getNext();
        return $next($this->getRequest(), $this->getResponse());
    }
}
