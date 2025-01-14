<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

use function gettype;
use function is_object;
use function sprintf;

/**
 * @template InstanceType
 * @extends AbstractPluginManager<InstanceType>
 */
class PluginManager extends AbstractPluginManager
{
    protected string $instanceOf = PluginInterface::class;

    public function validate(mixed $instance): void
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                $this->instanceOf,
                is_object($instance) ? $instance::class : gettype($instance)
            ));
        }
    }
}
