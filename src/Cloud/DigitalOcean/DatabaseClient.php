<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
