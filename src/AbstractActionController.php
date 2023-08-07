<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Event\ControllerEvent;
use Psr\Http\Message\ResponseInterface;

use function array_merge;
use function method_exists;
use function strtolower;
use function trim;

abstract class AbstractActionController extends AbstractController
{
    public function dispatch(): ResponseInterface
    {
        $action = strtolower(trim($this->request->getAttribute('action', 'index')));
        if (empty($action)) {
            $action = 'index';
        }

        $action = AbstractController::getMethodFromAction($action);

        if (method_exists($this, $action)) {
            $r = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH, [
                'request'    => $this->request,
                'handler'    => $this->getHandler(),
                'controller' => $this,
                'method'     => $action,
            ]);
            if ($r instanceof ResponseInterface) {
                return $r;
            }

            $this->request = $r->getParam('request');
            $response      = $this->$action();
            $params        = array_merge($r->getParams(), ['response' => $response]);

            $r = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH, $params);
            if ($r instanceof ResponseInterface) {
                return $r;
            }

            return $response;
        }

        //just go the next middleware, it will eventually hit a 404 if no one handles the request
        $handler = $this->getHandler();
        return $handler->handle($this->request);
    }
}
