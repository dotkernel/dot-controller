<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vrax
 * Date: 1/27/2017
 * Time: 7:12 PM
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
