<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Template\TemplateRendererInterface;

use function func_get_args;

class TemplatePlugin implements PluginInterface
{
    /** @var TemplateRendererInterface */
    protected $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function __invoke(?string $templateName = null, array $params = []): mixed
    {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    /**
     * @param array $params
     */
    public function render(string $templateName, array $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    /**
     * @param mixed $value
     */
    public function addDefaultParam(string $templateName, string $param, $value)
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
