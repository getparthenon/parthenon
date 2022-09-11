<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Repository\Dummy;

use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Parthenon\Athena\ResultSet;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet
    {
        return new ResultSet([], '', '', 10);
    }

    public function getById($id, $includeDeleted = false)
    {
        // TODO: Implement getById() method.
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    public function undelete($entity)
    {
        // TODO: Implement undelete() method.
    }

    public function getAllUnread(): array
    {
        return [];
    }

    public function findById($id)
    {
        // TODO: Implement findById() method.
    }
}
