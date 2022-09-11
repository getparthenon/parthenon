<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestHandlerTest extends TestCase
{
    public function testSupportsJson()
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');

        $handler = new JsonRequestHandler();

        $this->assertTrue($handler->supports($request));
    }

    public function testDoesNotSupportForm()
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('form');

        $handler = new JsonRequestHandler();

        $this->assertFalse($handler->supports($request));
    }
}
