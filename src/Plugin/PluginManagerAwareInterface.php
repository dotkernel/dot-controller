<?php
/**
 * @copyright: DotKernel
 * @package: dot-controller
 * @author: n3vrax
 * Date: 8/15/2016
 * Time: 5:56 PM
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