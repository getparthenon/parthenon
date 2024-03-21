<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
