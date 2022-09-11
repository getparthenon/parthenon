<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Cloud\DigitalOcean;

use Parthenon\Cloud\Exception\DigitalOcean\CantCreateDatabaseException;

interface DatabaseClientInterface
{
    /**
     * @throws CantCreateDatabaseException
     */
    public function createDatabase(string $clusterId, string $databaseName): void;
}
