<?php
/**
 * @copyright: DotKernel
 * @library: dot-controller
 * @author: n3vrax
 * Date: 1/27/2017
 * Time: 7:29 PM
 */

namespace Dot\Controller\Factory;

use Dot\Controller\Event\ControllerEventListenerAwareInterface;
use Dot\Controller\Event\ControllerEventListenerInterface;
use Dot\Controller\Exception\RuntimeException;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class ControllerEventListenersInitializer
 * @package Dot\Controller\Factory
 */
class ControllerEventListenersInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof ControllerEventListenerAwareInterface) {
            $config = $container->get('config')['dot_controller'];

            if (isset($config['event_listeners']) && is_array($config['event_listeners'])) {
                foreach ($config['event_listeners'] as $controller => $listeners) {
                    if (get_class($instance) === $controller
                        || $controller === ControllerEventListenerInterface::LISTEN_ALL) {
                        $listeners = (array) $listeners;
                        foreach ($listeners as $listener) {
                            $listener = $this->getControllerListener($container, $listener);
                            $instance->attachListener($listener);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param $listenerName
     * @return mixed
     */
    protected function getControllerListener(ContainerInterface $container, $listenerName)
    {
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
                is_object($listener) ? get_class($listener) : gettype($listener)
            ));
        }

        return $listener;
    }
}
