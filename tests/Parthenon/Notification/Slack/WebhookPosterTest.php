<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
