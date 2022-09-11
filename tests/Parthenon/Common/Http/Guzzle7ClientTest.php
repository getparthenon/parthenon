<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class Guzzle7ClientTest extends TestCase
{
    public function testCallsClient()
    {
        $request = $this->createMock(RequestInterface::class);
        $guzzle = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $guzzle->expects($this->once())->method('send')->with($request, []);

        $client = new Guzzle7Client($guzzle);
        $client->sendRequest($request);
    }

    public function testCallsClientWithArray()
    {
        $options = ['sds'];
        $request = $this->createMock(RequestInterface::class);
        $guzzle = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $guzzle->expects($this->once())->method('send')->with($request, $options);

        $client = new Guzzle7Client($guzzle);
        $client->sendRequest($request, $options);
    }
}
