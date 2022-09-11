<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use Parthenon\Common\Exception\RequestProcessor\NoValidRequestHandlerException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestHandlerManagerTest extends TestCase
{
    public function testReturnsHandler()
    {
        $request = $this->createMock(Request::class);
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $requestHandler->method('supports')->with($request)->willReturn(true);

        $mngr = new RequestHandlerManager();
        $mngr->addRequestHandler($requestHandler);
        $actual = $mngr->getRequestHandler($request);
        $this->assertSame($requestHandler, $actual);
    }

    public function testThrowsException()
    {
        $this->expectException(NoValidRequestHandlerException::class);

        $request = $this->createMock(Request::class);
        $requestHandler = $this->createMock(RequestHandlerInterface::class);

        $requestHandler->method('supports')->with($request)->willReturn(false);

        $mngr = new RequestHandlerManager();
        $mngr->addRequestHandler($requestHandler);
        $mngr->getRequestHandler($request);
    }
}
