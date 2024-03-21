<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\AbTesting\Decider\EnabledDecider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserAgentDeciderTest extends TestCase
{
    public function testReturnsFalseIfNoUserAgent()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $headerBag = $this->createMock(HeaderBag::class);
        $request = new Request();

        $request->headers = $headerBag;

        $requestStack->method('getCurrentRequest')->willReturn($request);
        $headerBag->method('get')->with($this->equalTo('User-Agent'))->willReturn(null);

        $userAgentDecider = new UserAgentDecider($requestStack, []);

        $this->assertFalse($userAgentDecider->isTestable());
    }

    public function testReturnsFalseIfNotFound()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $headerBag = $this->createMock(HeaderBag::class);
        $request = new Request();

        $request->headers = $headerBag;

        $requestStack->method('getCurrentRequest')->willReturn($request);
        $headerBag->method('get')->with($this->equalTo('User-Agent'))->willReturn('Fancy Browser');

        $userAgentDecider = new UserAgentDecider($requestStack, []);

        $this->assertTrue($userAgentDecider->isTestable());
    }

    public function testReturnsTrueIfFound()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $headerBag = $this->createMock(HeaderBag::class);
        $request = new Request();

        $request->headers = $headerBag;

        $requestStack->method('getCurrentRequest')->willReturn($request);
        $headerBag->method('get')->with($this->equalTo('User-Agent'))->willReturn('Fancy Browser');

        $userAgentDecider = new UserAgentDecider($requestStack, ['Fancy Browser']);

        $this->assertFalse($userAgentDecider->isTestable());
    }
}
