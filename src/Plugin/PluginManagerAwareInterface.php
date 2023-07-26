<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

/**
 * Interface PluginManagerAwareInterface
 */
interface PluginManagerAwareInterface
{
    /**
     * @return void
     */
    public function setPluginManager(PluginManager $plugins);

    public function getPluginManager(): PluginManager;
}
