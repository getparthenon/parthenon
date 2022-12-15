<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\TenantProvider;

use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Entity\TenantInterface;

final class TestCurrentTenantProvider implements TenantProviderInterface
{
    private static string $database;

    private static string $subdomain;

    public static function setTenantInfo(string $database, string $subdomain): void
    {
        self::$database = $database;
        self::$subdomain = $subdomain;
    }

    public function getCurrentTenant(bool $refresh = false): TenantInterface
    {
        return Tenant::createWithSubdomainAndDatabase(self::$database, self::$subdomain);
    }

    public function setTenant(TenantInterface $tenant): void
    {
        // TODO: Implement setTenant() method.
    }
}
