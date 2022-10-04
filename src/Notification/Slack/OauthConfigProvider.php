<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
