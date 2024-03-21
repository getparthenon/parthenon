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

use Elasticsearch\Client as ElasticsearchClient;
use Parthenon\Common\Exception\Elasticsearch\InvalidBodyException;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\LoggerAwareTrait;

final class Client implements ClientInterface
{
    use LoggerAwareTrait;

    public function __construct(private ElasticsearchClient $client)
    {
    }

    public function save(string $indexName, array $body)
    {
        if (!isset($body['id'])) {
            throw new InvalidBodyException('No id provided');
        }

        $body = [
            'index' => $indexName,
            'id' => $body['id'],
            'body' => [
                'doc' => $body,
                'doc_as_upsert' => true,
            ],
        ];

        try {
            $this->getLogger()->info('Sending elasticsearch update command', ['body' => $body]);
            $this->client->update($body);
        } catch (\Throwable $e) {
            throw new GeneralException('An error occurred when executing an elasticsearch update', $e->getCode(), $e);
        }
    }

    public function search(string $indexName, array $query): array
    {
        try {
            $this->getLogger()->info('Sending elasticsearch update command', ['index_name' => $indexName, 'query' => $query]);

            return $this->client->search([
                'index' => $indexName,
                'body' => $query,
            ]);
        } catch (\Throwable $e) {
            throw new GeneralException('An error occurred when executing an elasticsearch search', $e->getCode(), $e);
        }
    }

    public function delete(string $indexName, string|int $id): array
    {
        // convert id to string for elasticsearch indexing
        $id = (string) $id;
        try {
            $this->getLogger()->info('Sending elasticsearch delete command', ['index_name' => $indexName, 'id' => $id]);

            return $this->client->delete([
                'index' => $indexName,
                'id' => $id,
            ]);
        } catch (\Throwable $e) {
            throw new GeneralException('An error occurred when executing an elasticsearch delete', $e->getCode(), $e);
        }
    }

    public function createIndex(string $indexName): array
    {
        try {
            $this->getLogger()->info('Sending elasticsearch create index command', ['index_name' => $indexName]);

            return $this->client->indices()->create([
                'index' => $indexName,
            ]);
        } catch (\Throwable $e) {
            throw new GeneralException('An error occurred when executing an elasticsearch index create', $e->getCode(), $e);
        }
    }

    public function createAlias(string $indexName, string $aliasName): array
    {
        try {
            $this->getLogger()->info('Sending elasticsearch create alias command', ['index_name' => $indexName, 'alias_name' => $aliasName]);

            return $this->client->indices()->putAlias([
                'index' => $indexName,
                'name' => $aliasName,
            ]);
        } catch (\Throwable $e) {
            throw new GeneralException('An error occurred when executing an elasticsearch create alias', $e->getCode(), $e);
        }
    }
}
