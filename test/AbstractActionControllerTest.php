<?php

declare(strict_types=1);

namespace DotTest\Controller;

use Dot\Controller\Event\ControllerEvent;
use DotTest\Controller\Fixture\PassThroughHandler;
use DotTest\Controller\Fixture\TestController;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class AbstractActionControllerTest extends TestCase
{
    private TestController $controller;
    private PassThroughHandler $handler;

    protected function setUp(): void
    {
        $this->controller = new TestController();
        $this->handler    = new PassThroughHandler();
    }

    public function testMissingActionCallsTheNextHandler(): void
    {
        $response = $this->controller->process($this->request('missing'), $this->handler);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(1, $this->handler->calls);
        $this->assertSame([], $this->controller->calledActions);
    }

    public function testExistingActionReturnsItsResponse(): void
    {
        $response = $this->controller->process($this->request('Foo-Bar'), $this->handler);

        $this->assertSame('fooBar', (string) $response->getBody());
        $this->assertSame(0, $this->handler->calls);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function defaultActionProvider(): iterable
    {
        yield 'null'       => [null];
        yield 'empty'      => [''];
        yield 'whitespace' => ['  '];
        yield 'integer'    => [5];
    }

    #[DataProvider('defaultActionProvider')]
    public function testInvalidOrEmptyActionUsesIndex(mixed $action): void
    {
        $response = $this->controller->process($this->request($action), $this->handler);

        $this->assertSame('index', (string) $response->getBody());
    }

    public function testMissingActionAttributeUsesIndex(): void
    {
        $response = $this->controller->process(new ServerRequest(), $this->handler);

        $this->assertSame('index', (string) $response->getBody());
    }

    public function testZeroActionIsNotTreatedAsEmpty(): void
    {
        $response = $this->controller->process($this->request('0'), $this->handler);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->controller->calledActions);
    }

    public function testBeforeDispatchListenerResponseStopsTheAction(): void
    {
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            static fn (): TextResponse => new TextResponse('denied', 403)
        );

        $response = $this->controller->process($this->request('index'), $this->handler);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame([], $this->controller->calledActions);
    }

    public function testAfterDispatchListenerResponseReplacesTheActionResponse(): void
    {
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            static fn (): TextResponse => new TextResponse('replaced', 202)
        );

        $response = $this->controller->process($this->request('index'), $this->handler);

        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame(['index'], $this->controller->calledActions);
    }

    public function testListenerReturnValueThatIsNotAResponseIsIgnored(): void
    {
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            static fn (): string => 'not a response'
        );

        $response = $this->controller->process($this->request('index'), $this->handler);

        $this->assertSame('index', (string) $response->getBody());
    }

    public function testBeforeDispatchListenerCanReplaceTheRequest(): void
    {
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_BEFORE_DISPATCH,
            static function (ControllerEvent $event): void {
                $request = $event->getParam('request');
                if ($request instanceof ServerRequestInterface) {
                    $event->setParam('request', $request->withAttribute('fromListener', true));
                }
            }
        );

        $response = $this->controller->process($this->request('request-attribute'), $this->handler);

        $this->assertSame('changed', (string) $response->getBody());
    }

    public function testAfterDispatchEventHoldsTheBeforeParamsAndTheResponse(): void
    {
        $params = [];
        $this->controller->getEventManager()->attach(
            ControllerEvent::EVENT_CONTROLLER_AFTER_DISPATCH,
            static function (ControllerEvent $event) use (&$params): void {
                $params = $event->getParams();
            }
        );

        $this->controller->process($this->request('index'), $this->handler);

        $this->assertIsArray($params);
        $this->assertSame('indexAction', $params['method']);
        $this->assertSame($this->controller, $params['controller']);
        $this->assertSame($this->handler, $params['handler']);
        $this->assertInstanceOf(TextResponse::class, $params['response']);
    }

    private function request(mixed $action): ServerRequestInterface
    {
        return (new ServerRequest())->withAttribute('action', $action);
    }
}
