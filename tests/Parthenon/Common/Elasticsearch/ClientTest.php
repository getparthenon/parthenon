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
