<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
}
