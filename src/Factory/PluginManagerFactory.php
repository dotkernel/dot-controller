<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace Dot\Controller\Factory;

use Dot\Controller\Plugin\PluginManager;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class PluginManagerFactory
 * @package Dot\Controller\Factory
 */
class PluginManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PluginManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $pluginManager = new PluginManager($container, $container->get('config')['dk_controller']['plugin_manager']);

        //register the built in plugins, if the required component is present
        if ($container->has(UrlHelper::class)) {
            $pluginManager->setFactory('url', function (ContainerInterface $container) {
                return new UrlHelperPlugin($container->get(UrlHelper::class));
            });
        }

        if ($container->has(TemplateRendererInterface::class)) {
            $pluginManager->setFactory('template', function (ContainerInterface $container) {
                return new TemplatePlugin($container->get(TemplateRendererInterface::class));
            });
        }

        return $pluginManager;
    }
}