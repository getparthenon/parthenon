<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Slack;

use Chadhutchins\OAuth2\Client\Provider\Slack;

class OauthConfigProvider implements ConfigProviderInterface
{
    public function __construct(private Slack $provider)
    {
    }

    public function getAppData(string $code): array
    {
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        return $token->getValues();
    }
}
