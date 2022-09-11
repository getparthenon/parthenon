<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Cloud\DigitalOcean;

final class Client implements ClientInterface
{
    public function __construct(private \DigitalOceanV2\Client $client)
    {
    }

    public function database(): DatabaseClientInterface
    {
        return new DatabaseClient($this->client);
    }
}
