<?php

declare(strict_types=1);

namespace Dot\Controller;

use Dot\Controller\Event\ControllerEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function is_string;
use function method_exists;
use function strtolower;
use function trim;

abstract class AbstractActionController extends AbstractController
{
    public function dispatch(): ResponseInterface
    {
        $action = $this->request->getAttribute('action');
        $action = is_string($action) ? strtolower(trim($action)) : '';
        if ($action === '') {
            $action = 'index';
        }

        $action = AbstractController::getMethodFromAction($action);

        if (! method_exists($this, $action)) {
            // Go to the next middleware. If no middleware handles the request, the result is a 404 response.
            return $this->getHandler()->handle($this->request);
        }

        $event = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH, [
            'request'    => $this->request,
            'handler'    => $this->getHandler(),
            'controller' => $this,
            'method'     => $action,
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $request = $event->getParam('request');
        if ($request instanceof ServerRequestInterface) {
            $this->request = $request;
        }

        $response = $this->$action();

        $event = $this->dispatchEvent(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            [...$event->getParams(), 'response' => $response]
        );
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        return $response;
    }
}
