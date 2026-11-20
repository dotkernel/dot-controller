<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Template\TemplateRendererInterface;

class TemplatePlugin implements PluginInterface
{
    public function __construct(
        protected TemplateRendererInterface $template
    ) {
    }

    /**
     * Without a template name, return the plugin. Otherwise, render the template.
     *
     * @param array<non-empty-string, mixed>|object $params
     */
    public function __invoke(?string $templateName = null, array|object $params = []): self|string
    {
        if ($templateName === null || $templateName === '') {
            return $this;
        }

        return $this->render($templateName, $params);
    }

    /**
     * @param non-empty-string $templateName
     * @param array<non-empty-string, mixed>|object $params
     */
    public function render(string $templateName, array|object $params = []): string
    {
        return $this->template->render($templateName, $params);
    }

    /**
     * @param non-empty-string $templateName
     * @param non-empty-string $param
     */
    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
        $this->template->addDefaultParam($templateName, $param, $value);
    }
}
