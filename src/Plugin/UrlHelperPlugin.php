<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Mezzio\Helper\UrlHelper;

use function func_get_args;

class UrlHelperPlugin implements PluginInterface
{
    /** @var UrlHelper */
    protected $urlHelper;

    public function __construct(UrlHelper $helper)
    {
        $this->urlHelper = $helper;
    }

    /**
     * @param array $routeParams
     * @param array $queryParams
     * @param null $fragmentIdentifier
     * @param array $options
     * @return UrlHelperPlugin|string
     */
    public function __invoke(
        ?string $routeName = null,
        array $routeParams = [],
        $queryParams = [],
        $fragmentIdentifier = null,
        array $options = []
    ) {
        $args = func_get_args();
        if (empty($args)) {
            return $this;
        }

        return $this->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * @param array $routeParams
     * @param array $queryParams
     * @param null $fragmentIdentifier
     * @param array $options
     */
    public function generate(
        ?string $routeName = null,
        array $routeParams = [],
        $queryParams = [],
        $fragmentIdentifier = null,
        array $options = []
    ): string {
        return $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }
}
