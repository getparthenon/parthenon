<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\TenantProvider;

interface TenantProviderAwareInterface
{
    public function setTenantProvider(TenantProviderInterface $tenantProvider): void;
}
