<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
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
        $action = strtolower(trim($this->request->getAttribute('action', 'index')));
        if (empty($action)) {
            $action = 'index';
        }

        $action = AbstractController::getMethodFromAction($action);

        if (method_exists($this, $action)) {
            $r = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH, [
                'request' => $this->request,
                'delegate' => $this->getDelegate(),
                'controller' => $this,
                'method' => $action
            ]);
            if ($r instanceof ResponseInterface) {
                return $r;
            }

            $this->request = $r->getParam('request');
            $response = $this->$action();
            $params = array_merge($r->getParams(), ['response' => $response]);

            $r = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH, $params);
            if ($r instanceof ResponseInterface) {
                return $r;
            }

            return $response;
        }

        //just go the the next middleware, it will eventually hit a 404 if no one handles the request
        $delegate = $this->getDelegate();
        return $delegate->process($this->request);
    }
}
