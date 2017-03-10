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
        $request = $this->request;

        $action = strtolower(trim($request->getAttribute('action', 'index')));
        if (empty($action)) {
            $action = 'index';
        }

        $action = AbstractController::getMethodFromAction($action);

        if (method_exists($this, $action)) {
            $r = $this->dispatchEvent(ControllerEvent::EVENT_CONTROLLER_DISPATCH, [
                'request' => $request,
                'next' => $this->getNext(),
                'method' => $action
            ]);
            if ($r instanceof ResponseInterface) {
                return $r;
            }

            return $this->$action();
        }

        //just go the the next middleware, it will eventually hit a 404 if no one handles the request
        $next = $this->getNext();
        return $next($this->getRequest(), $this->getResponse());
    }
}
