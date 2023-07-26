<?php

declare(strict_types=1);

namespace Dot\Controller\Event;

use Laminas\EventManager\AbstractListenerAggregate;

abstract class AbstractControllerEventListener extends AbstractListenerAggregate implements
    ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;
}
