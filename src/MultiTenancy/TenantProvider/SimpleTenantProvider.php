<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
