<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Notification\Slack;

use Chadhutchins\OAuth2\Client\Provider\Slack;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PHPUnit\Framework\TestCase;

class OauthConfigProviderTest extends TestCase
{
    public function testCallsProviderAndReturnsArray()
    {
        $slack = $this->createMock(Slack::class);
        $accessToken = $this->createMock(AccessTokenInterface::class);

        $output = ['team' => ['name' => 'Test Name']];

        $slack->method('getAccesstoken')->with('authorization_code', ['code' => 'the_code'])->willReturn($accessToken);
        $accessToken->method('getValues')->willReturn($output);

        $oauthConfigProvider = new OauthConfigProvider($slack);
        $actual = $oauthConfigProvider->getAppData('the_code');

        $this->assertEquals($output, $actual);
    }
}
