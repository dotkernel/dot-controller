<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

namespace DotKernel\DotController\Plugin;

use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class TemplatePlugin
 * @package DotKernel\DotController\Plugin
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
     * @param string $name
     * @param array $params
     * @return string
     */
    public function render($name, array $params = [])
    {
        return $this->template->render($name, $params);
    }

    /**
     * @param string $templateName
     * @param string $param
     * @param mixed $value
     */
    public function addDefaultParam($templateName, $param, $value)
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}