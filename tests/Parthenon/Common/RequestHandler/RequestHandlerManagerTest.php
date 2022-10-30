<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
