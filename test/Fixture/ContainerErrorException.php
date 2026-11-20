<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

use function sprintf;

final class ContainerErrorException extends RuntimeException implements ContainerExceptionInterface
{
    public static function fromPrevious(string $id, Throwable $previous): self
    {
        return new self(sprintf('Service "%s" could not be created.', $id), 0, $previous);
    }
}
