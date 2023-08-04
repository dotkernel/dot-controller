<?php

declare(strict_types=1);

namespace DotTest\Controller\Plugin;

use Dot\Controller\Plugin\PluginInterface;
use Dot\Controller\Plugin\PluginManager as Subject;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class PluginManagerTest extends TestCase
{
    protected ContainerInterface|MockObject $container;
    protected Subject $subject;

    /**
     * @throws Exception
     */
    protected function setup(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->subject   = new Subject($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    public function testInstanceOf(): void
    {
        $plugin = $this->createMock(PluginInterface::class);
        $this->expectNotToPerformAssertions();
        $this->subject->validate($plugin);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function testInvalidInstanceOf(): void
    {
        $this->expectException(InvalidServiceException::class);
        $this->subject->validate('invalidPlugin');
    }
}
