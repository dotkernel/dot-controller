<?php

declare(strict_types=1);

namespace Dot\Controller\Factory;

use Dot\Controller\Exception\InvalidArgumentException;
use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function is_array;

class PluginManagerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container): PluginManager
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_array($config) ? ($config['dot_controller']['plugin_manager'] ?? []) : [];
        if (! is_array($config)) {
            throw new InvalidArgumentException('The "dot_controller.plugin_manager" config must be an array.');
        }

        $pluginManager = new PluginManager($container, $config);

        // Register the built-in plugins when the required service is present.
        if ($container->has(UrlHelper::class)) {
            $pluginManager->setFactory(
                'url',
                static function (ContainerInterface $container): UrlHelperPlugin {
                    $helper = $container->get(UrlHelper::class);
                    return new UrlHelperPlugin($helper);
                }
            );
        }

        if ($container->has(TemplateRendererInterface::class)) {
            $pluginManager->setFactory(
                'template',
                static function (ContainerInterface $container): TemplatePlugin {
                    $renderer = $container->get(TemplateRendererInterface::class);
                    return new TemplatePlugin($renderer);
                }
            );
        }

        return $pluginManager;
    }
}
