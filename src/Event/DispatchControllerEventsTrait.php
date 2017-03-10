<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Dot\Controller\AbstractController;
use Dot\Controller\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ResponseCollection;

/**
 * Class DispatchControllerEventsTrait
 * @package Dot\Controller\Event
 */
trait DispatchControllerEventsTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $data
     * @param null $target
     * @return ControllerEvent|ResponseCollection
     */
    public function dispatchEvent(string $name, array $data = [], $target = null)
    {
        if (!$this instanceof AbstractController) {
            throw new RuntimeException('Only controllers can dispatch controller events');
        }

        if ($target === null) {
            $target = $this;
        }

        $event = new ControllerEvent($name, $target, $data);
        $r = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        if ($r->stopped()) {
            return $r->last();
        }

        return $event;
    }
}
