<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\MultiTenancy\Entity\TenantInterface;

class TenantRepository extends DoctrineCrudRepository implements TenantRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findBySubdomain(string $subdomain): TenantInterface
    {
        $query = $this->entityRepository->createQueryBuilder('t')
            ->where('LOWER(t.subdomain) = LOWER(:subdomain)')
            ->setParameter(':subdomain', $subdomain)->getQuery();

        try {
            $tenant = $query->getSingleResult();
        } catch (\Throwable $e) {
            throw new NoEntityFoundException("Can't find ".$subdomain, previous: $e);
        }

        if (!$tenant instanceof TenantInterface) {
            throw new NoEntityFoundException('Unable to find Tenant for subdomain');
        }

        return $tenant;
    }
}
