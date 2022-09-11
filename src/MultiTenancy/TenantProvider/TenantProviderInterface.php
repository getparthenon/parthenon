<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\TenantProvider;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\MultiTenancy\Entity\TenantInterface;

interface TenantProviderInterface
{
    /**
     * @throws GeneralException
     */
    public function getCurrentTenant(bool $refresh = false): TenantInterface;
}
