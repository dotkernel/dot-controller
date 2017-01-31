<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin;

/**
 * Interface PluginManagerAwareInterface
 * @package Dot\Controller\Plugin
 */
interface PluginManagerAwareInterface
{
    /**
     * @param PluginManager $plugins
     * @return void
     */
    public function setPluginManager(PluginManager $plugins);

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager;
}
