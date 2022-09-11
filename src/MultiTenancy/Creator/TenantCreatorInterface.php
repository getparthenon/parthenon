<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Creator;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Parthenon\MultiTenancy\Exception\TenantCreationFailureException;

interface TenantCreatorInterface
{
    /**
     * @throws TenantCreationFailureException
     */
    public function createTenant(TenantInterface $tenant): void;
}
