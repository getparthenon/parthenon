<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
