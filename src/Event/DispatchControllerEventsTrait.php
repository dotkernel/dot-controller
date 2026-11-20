<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Dot\Controller\AbstractController;
use Dot\Controller\Exception\RuntimeException;
use Laminas\EventManager\EventManagerAwareTrait;
use Psr\Http\Message\ResponseInterface;

trait DispatchControllerEventsTrait
{
    use EventManagerAwareTrait;

    /**
     * Trigger a controller event.
     *
     * When a listener returns a response, the other listeners do not run and this method returns that response.
     * Otherwise, it returns the event, which can hold params that the listeners changed.
     *
     * @param array<string, mixed> $data
     * @return ControllerEvent<object|string, array<string, mixed>>|ResponseInterface
     * @throws RuntimeException
     */
    public function dispatchEvent(
        string $name,
        array $data = [],
        object|string|null $target = null
    ): ControllerEvent|ResponseInterface {
        if (! $this instanceof AbstractController) {
            throw new RuntimeException('Only controllers can dispatch controller events');
        }

        $event  = new ControllerEvent($name, $target ?? $this, $data);
        $result = $this->getEventManager()->triggerEventUntil(
            static fn (mixed $response): bool => $response instanceof ResponseInterface,
            $event
        );

        $last = $result->last();
        if ($result->stopped() && $last instanceof ResponseInterface) {
            return $last;
        }

        return $event;
    }
}
