<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Dot\Controller\Event\AbstractControllerEventListener;
use Dot\Controller\Event\ControllerEvent;

use function is_string;

final class TestListener extends AbstractControllerEventListener
{
    /** @var list<string> */
    public static array $log = [];

    public static function reset(): void
    {
        self::$log = [];
    }

    /**
     * @param ControllerEvent<object|string|null, array<string, mixed>> $e
     */
    public function onBeforeDispatch(ControllerEvent $e): void
    {
        $method      = $e->getParam('method');
        self::$log[] = 'before:' . (is_string($method) ? $method : '');
    }

    /**
     * @param ControllerEvent<object|string|null, array<string, mixed>> $e
     */
    public function onAfterDispatch(ControllerEvent $e): void
    {
        self::$log[] = 'after';
    }
}
