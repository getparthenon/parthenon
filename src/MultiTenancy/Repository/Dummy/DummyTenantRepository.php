<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
