<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Plugin\UrlHelperPlugin as Subject;
use Mezzio\Helper\UrlHelper;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlHelperPluginTest extends TestCase
{
    private UrlHelper|MockObject $urlHelper;
    private Subject $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->urlHelper = $this->createMock(UrlHelper::class);
        $this->subject   = new Subject($this->urlHelper);
    }

    public function testInvokeWithoutArguments(): void
    {
        $result = $this->subject->__invoke();

        $this->assertInstanceOf(Subject::class, $result);
    }

    public function testInvokeWithArguments(): void
    {
        $routeName          = 'test.route';
        $routeParams        = ['id' => 123];
        $queryParams        = ['sort' => 'asc'];
        $fragmentIdentifier = 'section';
        $options            = ['absolute' => true];

        $this->urlHelper->expects($this->once())
            ->method('generate')
            ->with($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options)
            ->willReturn('https://example.com/test-route?id=123&sort=asc#section');

        $result = $this->subject->__invoke($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);

        $this->assertSame('https://example.com/test-route?id=123&sort=asc#section', $result);
    }

    public function testGenerate(): void
    {
        $routeName          = 'test.route';
        $routeParams        = ['id' => 123];
        $queryParams        = ['sort' => 'asc'];
        $fragmentIdentifier = 'section';
        $options            = ['absolute' => true];

        $this->urlHelper->expects($this->once())
            ->method('generate')
            ->with($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options)
            ->willReturn('https://example.com/test-route?id=123&sort=asc#section');

        $result = $this->subject->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);

        $this->assertSame('https://example.com/test-route?id=123&sort=asc#section', $result);
    }
}
