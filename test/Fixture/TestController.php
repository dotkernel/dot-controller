<?php

declare(strict_types=1);

namespace DotTest\Controller\Fixture;

use Dot\Controller\AbstractActionController;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;

final class TestController extends AbstractActionController
{
    /** @var list<string> */
    public array $calledActions = [];

    public function indexAction(): ResponseInterface
    {
        $this->calledActions[] = 'index';

        return new TextResponse('index');
    }

    public function fooBarAction(): ResponseInterface
    {
        $this->calledActions[] = 'fooBar';

        return new TextResponse('fooBar');
    }

    public function requestAttributeAction(): ResponseInterface
    {
        $this->calledActions[] = 'requestAttribute';

        $value = $this->getRequest()->getAttribute('fromListener');

        return new TextResponse($value === null ? 'none' : 'changed');
    }
}
