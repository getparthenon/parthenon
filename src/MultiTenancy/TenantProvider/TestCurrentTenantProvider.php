<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
