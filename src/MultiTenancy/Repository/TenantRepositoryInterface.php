<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\MultiTenancy\Entity\TenantInterface;

interface TenantRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findBySubdomain(string $subdomain): TenantInterface;
}
