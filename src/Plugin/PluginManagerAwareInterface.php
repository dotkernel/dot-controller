<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace DotKernel\DotController\Plugin;

/**
 * Interface PluginManagerAwareInterface
 * @package DotKernel\DotController\Plugin
 */
interface PluginManagerAwareInterface
{
    /**
     * @param PluginManager $plugins
     * @return void
     */
    public function setPluginManager(PluginManager $plugins);

    /**
     * @return PluginInterface
     */
    public function getPluginManager();
}