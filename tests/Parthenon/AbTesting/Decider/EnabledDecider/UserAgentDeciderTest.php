<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
