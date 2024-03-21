<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
