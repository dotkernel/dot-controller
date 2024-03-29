<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Template\TemplateRendererInterface;

use function func_get_args;

class TemplatePlugin implements PluginInterface
{
    protected TemplateRendererInterface $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function __invoke(?string $templateName = null, array $params = []): self|string
    {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    public function render(string $templateName, array $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
