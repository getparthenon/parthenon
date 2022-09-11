<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Slack;

use Nyholm\Psr7\Request;
use Nyholm\Psr7\Stream;
use Parthenon\Common\Http\ClientInterface;

final class WebhookPoster implements WebhookPosterInterface
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function send(string $webhook, array $message)
    {
        $request = new Request('POST', $webhook, ['Content-Type' => 'application/json']);
        $request = $request->withBody(Stream::create(json_encode($message)));
        $this->client->sendRequest($request);
    }
}
