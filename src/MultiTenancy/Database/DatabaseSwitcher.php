<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Database;

use Parthenon\MultiTenancy\Dbal\TenantConnection;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\TenantProvider\SimpleTenantProvider;

final class DatabaseSwitcher implements DatabaseSwitcherInterface
{
    public function __construct(
        private TenantConnection $connection,
    ) {
    }

    public function switchToTenant(TenantInterface $tenant): void
    {
        $this->connection->setCurrentTenantProvider(new SimpleTenantProvider($tenant));
        $this->connection->connect(true);
    }
}
