<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @template InstanceType
 * @extends AbstractPluginManager<InstanceType>
 */
class PluginManager extends AbstractPluginManager
{
    /** @var string $instanceOf */
    protected $instanceOf = PluginInterface::class;
}
