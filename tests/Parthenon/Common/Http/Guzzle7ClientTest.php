<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
