<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Cloud\DigitalOcean;

use Parthenon\Cloud\Exception\DigitalOcean\CantCreateDatabaseException;

final class DatabaseClient implements DatabaseClientInterface
{
    public function __construct(private \DigitalOceanV2\Client $client)
    {
    }

    public function createDatabase(string $clusterId, string $databaseName): void
    {
        try {
            $this->client->database()->createDatabase($clusterId, $databaseName);
        } catch (\Throwable $e) {
            throw new CantCreateDatabaseException(previous: $e);
        }
    }
}
