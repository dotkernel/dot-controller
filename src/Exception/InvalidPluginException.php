<?php

declare(strict_types=1);

namespace Dot\Controller\Exception;

use Dot\Controller\Plugin\PluginInterface;
use Psr\Container\ContainerExceptionInterface;

use function get_debug_type;
use function sprintf;

class InvalidPluginException extends RuntimeException implements ContainerExceptionInterface
{
    public static function fromInstance(string $name, mixed $instance): self
    {
        return new self(sprintf(
            'Controller plugin "%s" must implement %s, %s given.',
            $name,
            PluginInterface::class,
            get_debug_type($instance)
        ));
    }

    public static function fromFactory(string $name, mixed $factory): self
    {
        return new self(sprintf(
            'The factory of controller plugin "%s" must be callable or the name of an invokable class, %s given.',
            $name,
            get_debug_type($factory)
        ));
    }
}
