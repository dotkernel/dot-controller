<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Dot\Controller\Plugin\PluginInterface;

use function strtoupper;

final class StringPlugin implements PluginInterface
{
    public function __invoke(string $value): string
    {
        return strtoupper($value);
    }
}
