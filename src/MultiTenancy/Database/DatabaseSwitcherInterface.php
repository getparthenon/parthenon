<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Database;

use Parthenon\MultiTenancy\Entity\TenantInterface;

interface DatabaseSwitcherInterface
{
    public function switchToTenant(TenantInterface $tenant): void;
}
