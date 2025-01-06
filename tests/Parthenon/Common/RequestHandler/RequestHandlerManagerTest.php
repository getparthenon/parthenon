<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
