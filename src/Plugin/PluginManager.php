<?php

declare(strict_types=1);

namespace Dot\Controller\Plugin;

use Dot\Controller\Exception\InvalidArgumentException;
use Dot\Controller\Exception\InvalidPluginException;
use Dot\Controller\Exception\PluginNotFoundException;
use Psr\Container\ContainerInterface;

use function array_diff;
use function array_keys;
use function class_exists;
use function implode;
use function is_array;
use function is_callable;
use function is_string;
use function sprintf;

/**
 * A PSR-11 container for controller plugins.
 *
 * It supports the "factories" and "aliases" keys of the "dot_controller.plugin_manager" config.
 * A factory receives the application container, the plugin name and the build options.
 */
class PluginManager implements ContainerInterface
{
    private const SUPPORTED_KEYS = ['factories', 'aliases'];

    /** @var array<string, callable|string> */
    private array $factories = [];

    /** @var array<string, string> */
    private array $aliases = [];

    /** @var array<string, PluginInterface> */
    private array $instances = [];

    /**
     * @param array<array-key, mixed> $config
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly ContainerInterface $container,
        array $config = [],
    ) {
        $unsupported = array_diff(array_keys($config), self::SUPPORTED_KEYS);
        if ($unsupported !== []) {
            throw new InvalidArgumentException(sprintf(
                'The controller plugin manager supports only the keys "%s". Remove the keys "%s".',
                implode('", "', self::SUPPORTED_KEYS),
                implode('", "', $unsupported)
            ));
        }

        foreach ($this->readMap($config, 'factories') as $name => $factory) {
            if (! is_callable($factory) && ! is_string($factory)) {
                throw InvalidPluginException::fromFactory($name, $factory);
            }
            $this->setFactory($name, $factory);
        }

        foreach ($this->readMap($config, 'aliases') as $alias => $target) {
            if (! is_string($target)) {
                throw new InvalidArgumentException(sprintf(
                    'The alias "%s" of the controller plugin manager must point to a plugin name.',
                    $alias
                ));
            }
            $this->setAlias($alias, $target);
        }
    }

    public function setFactory(string $name, callable|string $factory): void
    {
        $this->factories[$name] = $factory;
        unset($this->instances[$name]);
    }

    public function setAlias(string $alias, string $target): void
    {
        $this->aliases[$alias] = $target;
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$this->resolveName($id)]);
    }

    /**
     * Return the shared instance of a plugin. Create it on the first call.
     *
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function get(string $id): PluginInterface
    {
        $name = $this->resolveName($id);

        return $this->instances[$name] ??= $this->build($name);
    }

    /**
     * Create a new instance of a plugin on each call.
     *
     * @param array<string, mixed> $options
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function build(string $name, array $options = []): PluginInterface
    {
        $name    = $this->resolveName($name);
        $factory = $this->factories[$name] ?? throw PluginNotFoundException::fromName($name);

        if (is_string($factory) && ! is_callable($factory)) {
            if (! class_exists($factory)) {
                throw InvalidPluginException::fromFactory($name, $factory);
            }
            $factory = new $factory();
        }

        if (! is_callable($factory)) {
            throw InvalidPluginException::fromFactory($name, $factory);
        }

        $plugin = $factory($this->container, $name, $options);
        if (! $plugin instanceof PluginInterface) {
            throw InvalidPluginException::fromInstance($name, $plugin);
        }

        return $plugin;
    }

    /**
     * Follow the aliases until a name without an alias is found.
     *
     * @throws InvalidArgumentException
     */
    private function resolveName(string $name): string
    {
        $visited = [];
        while (isset($this->aliases[$name])) {
            if (isset($visited[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'The alias "%s" of the controller plugin manager is part of a cycle.',
                    $name
                ));
            }
            $visited[$name] = true;
            $name           = $this->aliases[$name];
        }

        return $name;
    }

    /**
     * @param array<array-key, mixed> $config
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    private function readMap(array $config, string $key): array
    {
        $map = $config[$key] ?? [];
        if (! is_array($map)) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" key of the controller plugin manager must be an array.',
                $key
            ));
        }

        $result = [];
        foreach ($map as $name => $value) {
            $result[(string) $name] = $value;
        }

        return $result;
    }
}
