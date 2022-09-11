<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Repository\Orm;

use Parthenon\Common\Repository\CustomServiceRepository;
use Parthenon\MultiTenancy\Entity\Tenant;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class TenantRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tenant::class);
    }
}
