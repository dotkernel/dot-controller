<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Dot\Controller\Plugin\PluginInterface;

final class PlainPlugin implements PluginInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly array $options = [],
    ) {
    }
}
