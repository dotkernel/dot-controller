<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller
 * @author: n3vrax
 * Date: 9/5/2016
 * Time: 8:24 PM
 */

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Zend\Expressive\Helper\UrlHelper;

/**
 * Class UrlHelperPlugin
 * @package Dot\Controller\Plugin
 */
class UrlHelperPlugin implements PluginInterface
{
    /** @var UrlHelper */
    protected $urlHelper;

    /**
     * UrlHelperPlugin constructor.
     * @param UrlHelper $helper
     */
    public function __construct(UrlHelper $helper)
    {
        $this->urlHelper = $helper;
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return string
     */
    public function generate(
        string $routeName,
        array $routeParams = []
    ) : string {
        return $this->urlHelper->generate(
            $routeName,
            $routeParams
        );
    }
}
