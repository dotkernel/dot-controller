<?php

declare(strict_types=1);

namespace Dot\Controller\Exception;

use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

class PluginNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function fromName(string $name): self
    {
        return new self(sprintf('Controller plugin "%s" is not registered.', $name));
    }
}
