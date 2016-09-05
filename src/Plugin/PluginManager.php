<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace DotKernel\DotController\Plugin;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package DotKernel\DotController\Plugin
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = PluginInterface::class;
}