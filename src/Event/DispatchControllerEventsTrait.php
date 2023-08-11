<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Dot\Controller\AbstractController;
use Dot\Controller\Exception\RuntimeException;
use Dot\Event\Event;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\ResponseCollection;
use Psr\Http\Message\ResponseInterface;

trait DispatchControllerEventsTrait
{
    use EventManagerAwareTrait;

    public function dispatchEvent(
        string $name,
        array $data = [],
        object|string|null $target = null
    ): Event|ResponseCollection {
        if (! $this instanceof AbstractController) {
            throw new RuntimeException('Only controllers can dispatch controller events');
        }

        if ($target === null) {
            $target = $this;
        }

        $event = new ControllerEvent($name, $target, $data);
        $r     = $this->getEventManager()->triggerEventUntil(function ($r) {
            return $r instanceof ResponseInterface;
        }, $event);

        if ($r->stopped()) {
            return $r->last();
        }

        return $event;
    }
}
