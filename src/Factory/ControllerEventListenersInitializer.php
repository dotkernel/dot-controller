<?php

declare(strict_types=1);

namespace Dot\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Exception\RuntimeException;
use Psr\Container\ContainerInterface;

use function class_exists;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;

class ControllerEventListenersInitializer
{
    public function __invoke(ContainerInterface $container, ?AbstractController $instance)
    {
        if ($instance instanceof AbstractController) {
            $this->attachControllerListeners($container, $instance);
        }
    }

    public function attachControllerListeners(ContainerInterface $container, AbstractController $controller)
    {
        $config = $container->get('config')['dot_controller'];

        if (isset($config['event_listeners']) && is_array($config['event_listeners'])) {
            foreach ($config['event_listeners'] as $ctrl => $listeners) {
                if (
                    $controller::class === $ctrl
                    || $ctrl === ControllerEventListenerInterface::LISTEN_ALL
                ) {
                    if (is_array($listeners)) {
                        foreach ($listeners as $listenerConfig) {
                            if (is_array($listenerConfig)) {
                                $listener = $listenerConfig['type'] ?? '';
                                $priority = (int) ($listenerConfig['priority'] ?? 1);

                                $listener = $this->getControllerListener($container, $listener);
                                $listener->attach($controller->getEventManager(), $priority);
                            } elseif (is_string($listenerConfig)) {
                                $listener = $listenerConfig;
                                $priority = 1;

                                $listener = $this->getControllerListener($container, $listener);
                                $listener->attach($controller->getEventManager(), $priority);
                            }
                        }
                    }
                }
            }
        }
    }

    protected function getControllerListener(
        ContainerInterface $container,
        string $listenerName
    ): ControllerEventListenerInterface {
        $listener = $listenerName;
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (! $listener instanceof ControllerEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Controller event listener must be an instance of %s, but %s was provided',
                ControllerEventListenerInterface::class,
                is_object($listener) ? $listener::class : gettype($listener)
            ));
        }

        return $listener;
    }
}
