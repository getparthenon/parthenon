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
