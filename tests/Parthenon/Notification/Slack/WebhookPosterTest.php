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

namespace Parthenon\Notification\Slack;

use Parthenon\Common\Http\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class WebhookPosterTest extends TestCase
{
    public const WEBHOOK = 'http://slack.example.org';
    public const JSON = ['data' => 'here'];

    public function testCallsClientWithRequest()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())->method('sendRequest')->with($this->callback(function (RequestInterface $request) {
            $request->getBody()->rewind();

            return 'POST' == $request->getMethod() && self::WEBHOOK == (string) $request->getUri() && json_encode(self::JSON) == (string) $request->getBody()->getContents();
        }));

        $webhookPoster = new WebhookPoster($client);
        $webhookPoster->send(self::WEBHOOK, self::JSON);
    }
}
