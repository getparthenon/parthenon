<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Elasticsearch;

interface ClientInterface
{
    public function save(string $indexName, array $body);

    public function search(string $indexName, array $query): array;

    public function delete(string $indexName, string|int $id): array;
}
