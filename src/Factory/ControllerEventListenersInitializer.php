<?php

declare(strict_types=1);

namespace Dot\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Exception\RuntimeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function class_exists;
use function get_debug_type;
use function is_array;
use function is_int;
use function is_numeric;
use function is_string;
use function sprintf;

class ControllerEventListenersInitializer
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    public function __invoke(ContainerInterface $container, mixed $instance): void
    {
        if ($instance instanceof AbstractController) {
            $this->attachControllerListeners($container, $instance);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    public function attachControllerListeners(ContainerInterface $container, AbstractController $controller): void
    {
        $config    = $container->has('config') ? $container->get('config') : [];
        $listeners = is_array($config) ? ($config['dot_controller']['event_listeners'] ?? []) : [];
        if (! is_array($listeners)) {
            return;
        }

        foreach ($listeners as $target => $entries) {
            if ($target !== $controller::class && $target !== ControllerEventListenerInterface::LISTEN_ALL) {
                continue;
            }
            if (! is_array($entries)) {
                continue;
            }

            foreach ($entries as $entry) {
                $name     = is_array($entry) ? ($entry['type'] ?? null) : $entry;
                $priority = is_array($entry) ? ($entry['priority'] ?? 1) : 1;

                if (! is_string($name) || $name === '') {
                    throw new RuntimeException(sprintf(
                        'Each controller event listener entry for `%s` must name a listener class or service,'
                        . ' %s given',
                        $controller::class,
                        get_debug_type($name)
                    ));
                }
                if (is_string($priority) && is_numeric($priority)) {
                    $priority = (int) $priority;
                }
                if (! is_int($priority)) {
                    throw new RuntimeException(sprintf(
                        'The priority of controller event listener `%s` must be an integer, %s given',
                        $name,
                        get_debug_type($priority)
                    ));
                }

                $this->getControllerListener($container, $name)->attach($controller->getEventManager(), $priority);
            }
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    protected function getControllerListener(
        ContainerInterface $container,
        string $listenerName
    ): ControllerEventListenerInterface {
        $listener = $container->has($listenerName) ? $container->get($listenerName) : $listenerName;
        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (! $listener instanceof ControllerEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Controller event listener must be an instance of %s, but %s was provided',
                ControllerEventListenerInterface::class,
                get_debug_type($listener)
            ));
        }

        return $listener;
    }
}
