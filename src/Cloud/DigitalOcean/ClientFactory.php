<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Cloud\DigitalOcean;

final class ClientFactory implements ClientFactoryInterface
{
    public function __construct(private string $apiKey)
    {
    }

    public function createClient(): ClientInterface
    {
        $digitalOceanClient = new \DigitalOceanV2\Client();
        $digitalOceanClient->authenticate($this->apiKey);

        return new Client($digitalOceanClient);
    }
}
