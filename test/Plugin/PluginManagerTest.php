<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Exception\InvalidArgumentException;
use Dot\Controller\Exception\InvalidPluginException;
use Dot\Controller\Plugin\PluginManager;
use DotTest\Controller\Fixture\InMemoryContainer;
use DotTest\Controller\Fixture\PlainPlugin;
use DotTest\Controller\Fixture\StringPlugin;
use DotTest\Controller\Fixture\StringPluginFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class PluginManagerTest extends TestCase
{
    private InMemoryContainer $container;

    protected function setUp(): void
    {
        $this->container = new InMemoryContainer();
    }

    public function testBuildsAPluginFromAFactoryClassName(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['string' => StringPluginFactory::class],
        ]);

        $this->assertTrue($pluginManager->has('string'));
        $this->assertInstanceOf(StringPlugin::class, $pluginManager->build('string'));
    }

    public function testCallsTheFactoryWithTheContainerTheNameAndTheOptions(): void
    {
        $arguments     = [];
        $pluginManager = new PluginManager($this->container);
        $pluginManager->setFactory(
            'plain',
            static function (
                ContainerInterface $container,
                string $name,
                array $options
            ) use (&$arguments): PlainPlugin {
                $arguments = [$container, $name];

                return new PlainPlugin($options);
            }
        );

        $plugin = $pluginManager->build('plain', ['key' => 'value']);

        $this->assertInstanceOf(PlainPlugin::class, $plugin);
        $this->assertSame(['key' => 'value'], $plugin->options);
        $this->assertSame([$this->container, 'plain'], $arguments);
    }

    public function testGetSharesAndBuildCreates(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['plain' => static fn (): PlainPlugin => new PlainPlugin()],
        ]);

        $this->assertSame($pluginManager->get('plain'), $pluginManager->get('plain'));
        $this->assertNotSame($pluginManager->build('plain'), $pluginManager->build('plain'));
    }

    public function testSetFactoryReplacesTheSharedInstance(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['plain' => static fn (): PlainPlugin => new PlainPlugin()],
        ]);
        $first         = $pluginManager->get('plain');

        $pluginManager->setFactory('plain', static fn (): PlainPlugin => new PlainPlugin());

        $this->assertNotSame($first, $pluginManager->get('plain'));
    }

    public function testResolvesAliases(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['string' => StringPluginFactory::class],
            'aliases'   => ['text' => 'str', 'str' => 'string'],
        ]);

        $this->assertTrue($pluginManager->has('text'));
        $this->assertSame($pluginManager->get('text'), $pluginManager->get('string'));
    }

    public function testAliasCycleThrows(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'aliases' => ['a' => 'b', 'b' => 'a'],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is part of a cycle');

        $pluginManager->has('a');
    }

    public function testUnknownPluginThrowsANotFoundException(): void
    {
        $pluginManager = new PluginManager($this->container);

        $this->assertFalse($pluginManager->has('unknown'));
        $this->expectException(NotFoundExceptionInterface::class);
        $this->expectExceptionMessage('Controller plugin "unknown" is not registered.');

        $pluginManager->get('unknown');
    }

    public function testPluginOfTheWrongTypeThrows(): void
    {
        $pluginManager = new PluginManager($this->container);
        $pluginManager->setFactory('invalid', static fn (): stdClass => new stdClass());

        $this->expectException(InvalidPluginException::class);
        $this->expectExceptionMessage('must implement Dot\Controller\Plugin\PluginInterface, stdClass given');

        $pluginManager->build('invalid');
    }

    public function testFactoryClassThatDoesNotExistThrows(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['missing' => 'Missing\Factory'],
        ]);

        $this->expectException(InvalidPluginException::class);

        $pluginManager->build('missing');
    }

    public function testFactoryClassThatIsNotInvokableThrows(): void
    {
        $pluginManager = new PluginManager($this->container, [
            'factories' => ['plain' => stdClass::class],
        ]);

        $this->expectException(InvalidPluginException::class);
        $this->expectExceptionMessage('must be callable or the name of an invokable class, stdClass given');

        $pluginManager->build('plain');
    }

    public function testFactoryOfTheWrongTypeThrows(): void
    {
        $this->expectException(InvalidPluginException::class);

        new PluginManager($this->container, ['factories' => ['plain' => 5]]);
    }

    public function testUnsupportedConfigKeyThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Remove the keys "invokables", "delegators".');

        new PluginManager($this->container, ['factories' => [], 'invokables' => [], 'delegators' => []]);
    }

    public function testConfigKeyThatIsNotAnArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "aliases" key of the controller plugin manager must be an array.');

        new PluginManager($this->container, ['aliases' => 'invalid']);
    }

    public function testAliasTargetThatIsNotAStringThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias "a" of the controller plugin manager must point to a plugin name.');

        new PluginManager($this->container, ['aliases' => ['a' => 5]]);
    }
}
