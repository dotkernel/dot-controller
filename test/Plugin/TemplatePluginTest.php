<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Plugin\TemplatePlugin as Subject;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TemplatePluginTest extends TestCase
{
    protected TemplateRendererInterface|MockObject $templateRenderer;
    protected Subject $subject;

    /**
     * @throws Exception
     */
    protected function setup(): void
    {
        $this->templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $this->subject          = new Subject($this->templateRenderer);
    }

    public function testInvokeWithoutArguments(): void
    {
        $result = $this->subject->__invoke();

        $this->assertInstanceOf(Subject::class, $result);
    }

    public function testInvokeWithArguments(): void
    {
        $templateName = 'test-template';
        $params       = ['param1' => 'value1', 'param2' => 'value2'];

        $this->templateRenderer->expects($this->once())
            ->method('render')
            ->with($templateName, $params)
            ->willReturn('<html><body>Test content</body></html>');

        $result = $this->subject->__invoke($templateName, $params);

        $this->assertSame('<html><body>Test content</body></html>', $result);
    }

    public function testRender(): void
    {
        $templateName = 'test-template';
        $params       = ['param1' => 'value1', 'param2' => 'value2'];

        $this->templateRenderer->expects($this->once())
            ->method('render')
            ->with($templateName, $params)
            ->willReturn('<html><body>Test content</body></html>');

        $result = $this->subject->render($templateName, $params);

        $this->assertSame('<html><body>Test content</body></html>', $result);
    }

    public function testAddDefaultParam(): void
    {
        $templateName = 'test-template';
        $param        = 'defaultParam';
        $value        = 'defaultValue';

        $this->templateRenderer->expects($this->once())
            ->method('addDefaultParam')
            ->with($templateName, $param, $value);

        $this->subject->addDefaultParam($templateName, $param, $value);
    }
}
