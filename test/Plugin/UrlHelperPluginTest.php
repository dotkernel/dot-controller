<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Plugin\UrlHelperPlugin;
use Mezzio\Helper\UrlHelper;
use PHPUnit\Framework\TestCase;

class UrlHelperPluginTest extends TestCase
{
    public function testInvokeWithoutArgumentsReturnsThePlugin(): void
    {
        $plugin = new UrlHelperPlugin($this->createStub(UrlHelper::class));

        $this->assertSame($plugin, $plugin());
    }

    public function testInvokeWithArgumentsGeneratesTheUrl(): void
    {
        $helper = $this->createMock(UrlHelper::class);
        $helper->expects($this->once())
            ->method('generate')
            ->with('test.route', ['id' => 123], ['sort' => 'asc'], 'section', ['reuse_result_params' => false])
            ->willReturn('/test/123?sort=asc#section');

        $result = (new UrlHelperPlugin($helper))(
            'test.route',
            ['id' => 123],
            ['sort' => 'asc'],
            'section',
            ['reuse_result_params' => false]
        );

        $this->assertSame('/test/123?sort=asc#section', $result);
    }

    public function testNullRouteNameMeansTheCurrentRoute(): void
    {
        $helper = $this->createMock(UrlHelper::class);
        $helper->expects($this->once())
            ->method('generate')
            ->with(null, ['id' => 5], [], null, [])
            ->willReturn('/current/5');

        $this->assertSame('/current/5', (new UrlHelperPlugin($helper))(null, ['id' => 5]));
    }

    public function testGenerate(): void
    {
        $helper = $this->createMock(UrlHelper::class);
        $helper->expects($this->once())
            ->method('generate')
            ->with('test.route', [], [], null, [])
            ->willReturn('/test');

        $this->assertSame('/test', (new UrlHelperPlugin($helper))->generate('test.route'));
    }
}
