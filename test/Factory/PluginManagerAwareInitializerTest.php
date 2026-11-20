<?php

declare(strict_types=1);

namespace DotTest\Controller\Factory;

use Dot\Controller\Factory\PluginManagerAwareInitializer;
use Dot\Controller\Plugin\PluginManager;
use DotTest\Controller\Fixture\InMemoryContainer;
use DotTest\Controller\Fixture\TestController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class PluginManagerAwareInitializerTest extends TestCase
{
    private PluginManagerAwareInitializer $initializer;

    protected function setUp(): void
    {
        $this->initializer = new PluginManagerAwareInitializer();
    }

    public function testSetsThePluginManager(): void
    {
        $container     = new InMemoryContainer();
        $pluginManager = new PluginManager($container);
        $container->set(PluginManager::class, $pluginManager);
        $controller = new TestController();

        ($this->initializer)($container, $controller);

        $this->assertSame($pluginManager, $controller->getPluginManager());
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function debugProvider(): iterable
    {
        yield 'true'         => [true, true];
        yield 'int 1'        => [1, true];
        yield 'string 1'     => ['1', true];
        yield 'string true'  => ['true', true];
        yield 'false'        => [false, false];
        yield 'int 0'        => [0, false];
        yield 'string false' => ['false', false];
        yield 'null'         => [null, false];
    }

    #[DataProvider('debugProvider')]
    public function testSetsTheDebugFlag(mixed $debug, bool $expected): void
    {
        $container  = $this->container(['debug' => $debug]);
        $controller = new TestController();

        ($this->initializer)($container, $controller);

        $this->assertSame($expected, $controller->isDebug());
    }

    public function testMissingDebugKeyMeansFalse(): void
    {
        $controller = new TestController();
        $controller->setDebug(true);

        ($this->initializer)($this->container([]), $controller);

        $this->assertFalse($controller->isDebug());
    }

    public function testIgnoresOtherInstances(): void
    {
        $this->expectNotToPerformAssertions();

        ($this->initializer)(new InMemoryContainer(), new stdClass());
        ($this->initializer)(new InMemoryContainer());
    }

    /**
     * @param array<string, mixed> $config
     */
    private function container(array $config): InMemoryContainer
    {
        $container = new InMemoryContainer(['config' => $config]);
        $container->set(PluginManager::class, new PluginManager($container));

        return $container;
    }
}
