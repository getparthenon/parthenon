<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
