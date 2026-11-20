<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\Exception\PluginNotFoundException;
use Dot\Controller\Exception\RuntimeException;
use Dot\Controller\Plugin\PluginManager;
use DotTest\Controller\Fixture\InMemoryContainer;
use DotTest\Controller\Fixture\PassThroughHandler;
use DotTest\Controller\Fixture\PlainPlugin;
use DotTest\Controller\Fixture\StringPluginFactory;
use DotTest\Controller\Fixture\TestController;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;

class AbstractControllerTest extends TestCase
{
    private TestController $controller;
    private PluginManager $pluginManager;

    protected function setUp(): void
    {
        $this->pluginManager = new PluginManager(new InMemoryContainer(), [
            'factories' => [
                'string' => StringPluginFactory::class,
                'plain'  => static fn (): PlainPlugin => new PlainPlugin(),
            ],
        ]);
        $this->controller    = new TestController();
    }

    public function testGetMethodFromAction(): void
    {
        $this->assertSame('testThisActionNameAction', TestController::getMethodFromAction('test.this-action_name'));
    }

    public function testGetPluginManager(): void
    {
        $this->controller->setPluginManager($this->pluginManager);

        $this->assertSame($this->pluginManager, $this->controller->getPluginManager());
    }

    public function testNoPluginManager(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Controller plugin manager not set');

        $this->controller->getPluginManager();
    }

    public function testProcessStoresTheRequestAndTheHandler(): void
    {
        $request = new ServerRequest();
        $handler = new PassThroughHandler();

        $this->controller->process($request, $handler);

        $this->assertSame($request, $this->controller->getRequest());
        $this->assertSame($handler, $this->controller->getHandler());
    }

    public function testDebug(): void
    {
        $this->assertFalse($this->controller->isDebug());

        $this->controller->setDebug(true);

        $this->assertTrue($this->controller->isDebug());
    }

    public function testPluginCreatesANewInstanceOnEachCall(): void
    {
        $this->controller->setPluginManager($this->pluginManager);

        $this->assertNotSame($this->controller->plugin('plain'), $this->controller->plugin('plain'));
    }

    public function testMagicCallReturnsAPluginThatIsNotInvokable(): void
    {
        $this->controller->setPluginManager($this->pluginManager);

        $this->assertInstanceOf(PlainPlugin::class, $this->controller->__call('plain', []));
    }

    public function testMagicCallInvokesAnInvokablePlugin(): void
    {
        $this->controller->setPluginManager($this->pluginManager);

        $this->assertSame('TEST', $this->controller->__call('string', ['test']));
    }

    public function testMagicCallWithAnUnknownPluginThrows(): void
    {
        $this->controller->setPluginManager($this->pluginManager);

        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage('Controller plugin "unknown" is not registered.');

        $this->controller->__call('unknown', []);
    }
}
