<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\AbstractActionController;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AbstractActionControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testNonExistingActionWillReturnResponse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $request->expects($this->any())->method('getAttribute')->withAnyParameters()->willReturn('test');

        $subject = new class extends AbstractActionController {
        };

        $response = $subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @throws Exception
     */
    public function testExistingActionWillReturnResponse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $request->expects($this->any())->method('getAttribute')->withAnyParameters()->willReturn('index');

        $subject = new class extends AbstractActionController {
            public function indexAction(): ResponseInterface
            {
                return new Response();
            }
        };

        $response = $subject->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
