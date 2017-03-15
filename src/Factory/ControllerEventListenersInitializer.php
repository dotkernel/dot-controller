<?php
/**
 * @see https://github.com/dotkernel/dot-controller/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Factory;

use Dot\Controller\AbstractController;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Exception\RuntimeException;
use Psr\Container\ContainerInterface;

/**
 * Class ControllerEventListenersInitializer
 * @package Dot\Controller\Factory
 */
class ControllerEventListenersInitializer
{
    /**
     * @param ContainerInterface $container
     * @param $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof AbstractController) {
            $this->attachControllerListeners($container, $instance);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param AbstractController $controller
     */
    public function attachControllerListeners(ContainerInterface $container, AbstractController $controller)
    {
        $config = $container->get('config')['dot_controller'];

        if (isset($config['event_listeners']) && is_array($config['event_listeners'])) {
            foreach ($config['event_listeners'] as $ctrl => $listeners) {
                if (get_class($controller) === $ctrl
                    || $ctrl === ControllerEventListenerInterface::LISTEN_ALL
                ) {
                    if (is_array($listeners)) {
                        foreach ($listeners as $listenerConfig) {
                            if (is_array($listenerConfig)) {
                                $listener = $listenerConfig['type'] ?? '';
                                $priority = (int)($listenerConfig['priority'] ?? 1);

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

    /**
     * @param ContainerInterface $container
     * @param string $listenerName
     * @return ControllerEventListenerInterface
     */
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

        if (!$listener instanceof ControllerEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Controller event listener must be an instance of %s, but %s was provided',
                ControllerEventListenerInterface::class,
                is_object($listener) ? get_class($listener) : gettype($listener)
            ));
        }

        return $listener;
    }
}
