<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
