<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
