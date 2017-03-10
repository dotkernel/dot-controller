<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Event;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractControllerEventListener
 * @package Dot\Controller\Event
 */
abstract class AbstractControllerEventListener extends AbstractListenerAggregate implements
    ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;
}
