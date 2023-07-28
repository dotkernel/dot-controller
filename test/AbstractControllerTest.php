<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\AbstractController as Subject;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginManager;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class AbstractControllerTest extends TestCase
{
    private PluginManager $pluginManager;
    private Subject $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->pluginManager = $this->createMock(PluginManager::class);
        $this->subject       = new class extends Subject {
            public function dispatch(): ResponseInterface
            {
                return new Response();
            }
        };
    }

    public function testGetMethodFromAction()
    {
        $testAction = 'test.this-action_name';
        $method     = Subject::getMethodFromAction($testAction);
        $this->assertSame('testThisActionNameAction', $method);
    }

    public function testGetPluginManager()
    {
        $this->subject->setPluginManager($this->pluginManager);
        $this->assertInstanceOf(PluginManager::class, $this->subject->getPluginManager());
    }

    public function testNoPluginManager()
    {
        $this->expectException(RuntimeException::class);
        $this->subject->getPluginManager();
    }
}
