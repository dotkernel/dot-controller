<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Closure;
use Psr\Container\ContainerInterface;
use Throwable;

use function array_key_exists;

/**
 * A minimal PSR-11 container for the tests.
 *
 * A service that is a Closure is a factory: the container calls it with itself on each get().
 */
final class InMemoryContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(
        private array $services = [],
    ) {
    }

    public function set(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->services)) {
            throw ServiceNotFoundException::fromId($id);
        }

        $service = $this->services[$id];
        if (! $service instanceof Closure) {
            return $service;
        }

        try {
            return $service($this);
        } catch (Throwable $exception) {
            throw ContainerErrorException::fromPrevious($id, $exception);
        }
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
