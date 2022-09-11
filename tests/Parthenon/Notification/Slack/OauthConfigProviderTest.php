<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
