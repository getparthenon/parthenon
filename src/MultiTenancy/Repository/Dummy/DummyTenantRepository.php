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

namespace Parthenon\MultiTenancy\Repository\Dummy;

use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;

final class DummyTenantRepository implements TenantRepositoryInterface
{
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function getById($id, $includeDeleted = false)
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function save($entity)
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function delete($entity)
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function undelete($entity)
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function findById($id)
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function findBySubdomain(string $subdomain): Tenant
    {
        throw new GeneralException('Dummy repository please set up real one');
    }

    public function getByIds(array $ids): ResultSet
    {
        throw new GeneralException('Dummy repository please set up real one');
    }
}
