<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Helper\UrlHelper;

use function func_num_args;

class UrlHelperPlugin implements PluginInterface
{
    public function __construct(
        protected UrlHelper $urlHelper
    ) {
    }

    /**
     * Without arguments, return the plugin. Otherwise, generate a URL.
     * A null route name means the route that matched the current request.
     *
     * @param non-empty-string|null $routeName
     * @param array<string, mixed> $routeParams
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    public function __invoke(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): self|string {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * @param non-empty-string|null $routeName
     * @param array<string, mixed> $routeParams
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $options
     */
    public function generate(
        ?string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): string {
        return $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }
}
