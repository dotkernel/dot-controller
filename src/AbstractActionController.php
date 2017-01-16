<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace Dot\Controller;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractActionController
 * @package Dot\Controller
 */
class AbstractActionController extends AbstractController
{
    /**
     * @return ResponseInterface
     */
    public function dispatch()
    {
        $request = $this->request;
        $action = AbstractController::getMethodFromAction(
            strtolower($request->getAttribute('action', 'index'))
        );

        if (method_exists($this, $action)) {
            return $this->$action();
        }

        //just go the the next middleware, it will eventually hit a 404 if no one handles the request
        $next = $this->getNext();
        return $next($this->getRequest(), $this->getResponse());
    }
}
