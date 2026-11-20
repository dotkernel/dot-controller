<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Stands for the next middleware. It answers 404 and counts its calls.
 */
final class PassThroughHandler implements RequestHandlerInterface
{
    public int $calls = 0;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->calls++;

        return new TextResponse('next', 404);
    }
}
