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

namespace Parthenon\Common\Elasticsearch;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testCloudBasedConnection()
    {
        $config = new Config();
        $config->setConnectionType(Config::CONNECTION_TYPE_CLOUD);

        $this->assertTrue($config->isCloudBasedConnection());
        $this->assertFalse($config->isNormalConnection());
    }

    public function testNormalBasedConnection()
    {
        $config = new Config();
        $config->setConnectionType(Config::CONNECTION_TYPE_NORMAL);

        $this->assertTrue($config->isNormalConnection());
        $this->assertFalse($config->isCloudBasedConnection());
    }

    public function testBasicAuth()
    {
        $config = new Config();

        $this->assertFalse($config->hasBasicAuthSettings());

        $config->setBasicUsername('username');
        $this->assertFalse($config->hasBasicAuthSettings());

        $config->setBasicPassword('password');
        $this->assertTrue($config->hasBasicAuthSettings());
    }

    public function testHasApi()
    {
        $config = new Config();

        $this->assertFalse($config->hasApiSettings());

        $config->setApiId('api');
        $this->assertFalse($config->hasApiSettings());

        $config->setApiKey('api_key');
        $this->assertTrue($config->hasApiSettings());
    }

    public function testHasHosts()
    {
        $config = new Config();

        $this->assertFalse($config->hasHosts());

        $config->setHosts(['https://host.example.org']);
        $this->assertTrue($config->hasHosts());
    }
}
