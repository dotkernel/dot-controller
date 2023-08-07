<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

interface PluginManagerAwareInterface
{
    public function setPluginManager(PluginManager $plugins): void;

    public function getPluginManager(): ?PluginManager;
}
