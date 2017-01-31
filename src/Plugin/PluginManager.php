<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Dot\Controller\Plugin
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = PluginInterface::class;
}
