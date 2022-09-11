<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Database;

use Parthenon\MultiTenancy\Entity\TenantInterface;

interface MigrationsHandlerInterface
{
    public function handleMigrations(TenantInterface $tenant): void;
}
