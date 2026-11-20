<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Plugin\TemplatePlugin;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class TemplatePluginTest extends TestCase
{
    public function testInvokeWithoutArgumentsReturnsThePlugin(): void
    {
        $plugin = new TemplatePlugin($this->createStub(TemplateRendererInterface::class));

        $this->assertSame($plugin, $plugin());
        $this->assertSame($plugin, $plugin(null));
        $this->assertSame($plugin, $plugin(''));
    }

    public function testInvokeWithANameRendersTheTemplate(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->once())
            ->method('render')
            ->with('test::template', ['key' => 'value'])
            ->willReturn('content');

        $this->assertSame('content', (new TemplatePlugin($renderer))('test::template', ['key' => 'value']));
    }

    public function testRenderAcceptsAnObject(): void
    {
        $params   = new stdClass();
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->once())
            ->method('render')
            ->with('test::template', $params)
            ->willReturn('content');

        $this->assertSame('content', (new TemplatePlugin($renderer))->render('test::template', $params));
    }

    public function testAddDefaultParam(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->once())
            ->method('addDefaultParam')
            ->with('test::template', 'key', 'value');

        (new TemplatePlugin($renderer))->addDefaultParam('test::template', 'key', 'value');
    }
}
