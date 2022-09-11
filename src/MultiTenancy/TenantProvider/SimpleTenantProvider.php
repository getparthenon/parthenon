<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\TenantProvider;

use Parthenon\MultiTenancy\Entity\TenantInterface;

class SimpleTenantProvider implements TenantProviderInterface
{
    public function __construct(private TenantInterface $tenant)
    {
    }

    public function setTenant(TenantInterface $tenant): void
    {
        // TODO: Implement setTenant() method.
    }

    public function getCurrentTenant(bool $refresh = false): TenantInterface
    {
        return $this->tenant;
    }
}
