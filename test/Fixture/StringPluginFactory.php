<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Psr\Container\ContainerInterface;

final class StringPluginFactory
{
    public function __invoke(ContainerInterface $container): StringPlugin
    {
        return new StringPlugin();
    }
}
