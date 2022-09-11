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
