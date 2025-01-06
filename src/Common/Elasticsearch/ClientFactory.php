<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Common\Elasticsearch;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder;

final class ClientFactory
{
    private ElasticsearchClient $rawClient;

    public function __construct(private Config $config)
    {
    }

    public function buildRawClient(): ElasticsearchClient
    {
        /*if (isset($this->rawClient)) {
            return $this->rawClient;
        }*/

        $builder = ClientBuilder::create();

        if ($this->config->isNormalConnection()) {
            $builder->setHosts($this->config->getHosts());
        }

        if ($this->config->isCloudBasedConnection()) {
            $builder->setElasticCloudId($this->config->getElasticCloudId());
        }

        if ($this->config->hasApiSettings()) {
            $builder->setApiKey($this->config->getApiId(), $this->config->getApiKey());
        }

        if ($this->config->hasBasicAuthSettings()) {
            $builder->setBasicAuthentication($this->config->getBasicUsername(), $this->config->getBasicPassword());
        }

        return $this->rawClient = $builder->build();
    }

    public function build(): Client
    {
        return new Client($this->buildRawClient());
    }
}
