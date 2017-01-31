<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin;

use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class TemplatePlugin
 * @package Dot\Controller\Plugin
 */
class TemplatePlugin implements PluginInterface
{
    /** @var TemplateRendererInterface */
    protected $template;

    /**
     * TemplatePlugin constructor.
     * @param TemplateRendererInterface $template
     */
    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     */
    public function render(string $templateName, array $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    /**
     * @param string $templateName
     * @param string $param
     * @param mixed $value
     */
    public function addDefaultParam(string $templateName, string $param, mixed $value)
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
