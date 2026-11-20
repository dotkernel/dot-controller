<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

final class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Service "%s" is not registered.', $id));
    }
}
