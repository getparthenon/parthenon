<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Slack;

use Chadhutchins\OAuth2\Client\Provider\Slack;

class OauthFactory
{
    public function __construct(private string $clientId, private string $clientSecret, private string $redirectUrl)
    {
    }

    public function getProvider(): Slack
    {
        return new \Chadhutchins\OAuth2\Client\Provider\Slack([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUrl,
        ]);
    }
}
