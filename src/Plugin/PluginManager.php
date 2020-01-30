<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Dot\Controller\Plugin
 */
class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = PluginInterface::class;
}
