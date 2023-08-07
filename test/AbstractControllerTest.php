<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\AbstractController as Subject;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginInterface;
use Dot\Controller\Plugin\PluginManager;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AbstractControllerTest extends TestCase
{
    private PluginManager|MockObject $pluginManager;
    private PluginInterface|MockObject $plugin;
    private ServerRequestInterface|MockObject $request;
    private RequestHandlerInterface|MockObject $handler;
    private Subject $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->pluginManager = $this->createMock(PluginManager::class);
        $this->plugin        = $this->createMock(PluginInterface::class);
        $this->request       = $this->createMock(ServerRequestInterface::class);
        $this->handler       = $this->createMock(RequestHandlerInterface::class);
        $this->subject       = new class extends Subject {
            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };
    }

    public function testGetMethodFromAction(): void
    {
        $testAction = 'test.this-action_name';
        $method     = Subject::getMethodFromAction($testAction);
        $this->assertSame('testThisActionNameAction', $method);
    }

    public function testGetPluginManager(): void
    {
        $this->subject->setPluginManager($this->pluginManager);
        $this->assertInstanceOf(PluginManager::class, $this->subject->getPluginManager());
    }

    public function testNoPluginManager(): void
    {
        $this->expectException(RuntimeException::class);
        $this->subject->getPluginManager();
    }

    public function testGetRequest(): void
    {
        $this->subject->process($this->request, $this->handler);
        $this->assertSame($this->request, $this->subject->getRequest());
    }

    public function testGetHandler(): void
    {
        $this->subject->process($this->request, $this->handler);
        $this->assertSame($this->handler, $this->subject->getHandler());
    }

    public function testDebug(): void
    {
        $this->assertFalse($this->subject->isDebug());

        $this->subject->setDebug(true);
        $this->assertTrue($this->subject->isDebug());
    }

    public function testCallPlugin(): void
    {
        $this->pluginManager->expects($this->once())
            ->method('get')
            ->with('somePlugin')
            ->willReturn($this->plugin);

        $controller = new class ($this->pluginManager) extends Subject {
            public function __construct(PluginManager $pluginManager)
            {
                $this->pluginManager = $pluginManager;
            }

            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };

        $plugin = $controller->somePlugin();

        $this->assertInstanceOf(PluginInterface::class, $plugin);
    }

    public function testCallCallablePlugin(): void
    {
        $mockCallablePlugin = function ($param) {
            return "Mock plugin called with parameter: $param";
        };

        $this->pluginManager->expects($this->once())
            ->method('get')
            ->with('callablePlugin')
            ->willReturn($mockCallablePlugin);

        $this->subject = new class ($this->pluginManager) extends Subject {
            public function __construct(PluginManager $pluginManager)
            {
                $this->pluginManager = $pluginManager;
            }

            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };

        $result = $this->subject->callablePlugin('paramValue');

        $this->assertSame('Mock plugin called with parameter: paramValue', $result);
    }
}
