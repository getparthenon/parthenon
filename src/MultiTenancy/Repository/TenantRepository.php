<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
