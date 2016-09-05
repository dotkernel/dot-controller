<?php
/**
 * @copyright: DotKernel
 * @package: dot-controller
 * @author: n3vrax
 * Date: 8/15/2016
 * Time: 5:56 PM
 */

namespace DotKernel\DotController;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractActionController
 * @package DotKernel\DotController
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
            strtolower($request->getAttribute('action', 'index')));

        if (method_exists($this, $action)) {
            return $this->$action();
        }

        //just go the the next middleware, it will eventually hit a 404 if no one handles the request
        $next = $this->getNext();
        return $next($this->getRequest(), $this->getResponse());
    }
}