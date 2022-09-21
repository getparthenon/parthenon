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
use Elasticsearch\Namespaces\IndicesNamespace;
use Parthenon\Common\Exception\Elasticsearch\InvalidBodyException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testThrowsExceptionOnSaveWhenNoIdeGiven()
    {
        $this->expectException(InvalidBodyException::class);

        $esClient = $this->createMock(ElasticsearchClient::class);
        $client = new Client($esClient);
        $client->save('index', []);
    }

    public function testCallsElasticsearchClient()
    {
        $document = [
            'id' => 'valid_id',
            'name' => 'Sally Brown',
        ];

        $esClient = $this->createMock(ElasticsearchClient::class);
        $esClient->expects($this->once())->method('update')->with([
            'index' => 'index_name',
            'id' => 'valid_id',
            'body' => [
                'doc' => $document,
                'doc_as_upsert' => true,
            ],
        ]);

        $client = new Client($esClient);
        $client->save('index_name', $document);
    }

    public function testDeleteCallsElasticsearchClient()
    {
        $result = ['body' => ['valid' => true]];

        $esClient = $this->createMock(ElasticsearchClient::class);
        $esClient->method('delete')->with(['index' => 'index_name', 'id' => 30])->willReturn($result);

        $client = new Client($esClient);

        $this->assertEquals($result, $client->delete('index_name', 30));
    }

    public function testCreateIndexCallsElasticsearchClient()
    {
        $result = ['body' => ['valid' => true]];

        $esClient = $this->createMock(ElasticsearchClient::class);
        $indices = $this->createMock(IndicesNamespace::class);

        $esClient->method('indices')->willReturn($indices);
        $indices->method('create')->with(['index' => 'index_name'])->willReturn($result);

        $client = new Client($esClient);

        $this->assertEquals($result, $client->createIndex('index_name'));
    }

    public function testCreateAliasElasticsearchClient()
    {
        $result = ['body' => ['valid' => true]];

        $esClient = $this->createMock(ElasticsearchClient::class);
        $indices = $this->createMock(IndicesNamespace::class);

        $esClient->method('indices')->willReturn($indices);
        $indices->method('putAlias')->with(['index' => 'index_name', 'name' => 'alias_name'])->willReturn($result);

        $client = new Client($esClient);

        $this->assertEquals($result, $client->createAlias('index_name', 'alias_name'));
    }
}
