<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Helper\UrlHelper;

use function func_get_args;

class UrlHelperPlugin implements PluginInterface
{
    protected UrlHelper $urlHelper;

    public function __construct(UrlHelper $helper)
    {
        $this->urlHelper = $helper;
    }

    public function __invoke(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        mixed $fragmentIdentifier = null,
        array $options = []
    ): self|string {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    public function generate(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        mixed $fragmentIdentifier = null,
        array $options = []
    ): string {
        return $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }
}
