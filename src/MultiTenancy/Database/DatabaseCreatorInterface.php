<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Database;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\MultiTenancy\Entity\TenantInterface;

interface DatabaseCreatorInterface
{
    /**
     * @throws GeneralException
     */
    public function createDatabase(TenantInterface $tenant): void;
}
