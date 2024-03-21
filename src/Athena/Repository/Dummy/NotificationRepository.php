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

namespace Parthenon\Athena\Repository\Dummy;

use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Parthenon\Athena\ResultSet;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null, $firstId = null): ResultSet
    {
        return new ResultSet([], $idKey, '', 10);
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

    public function getByIds(array $ids): ResultSet
    {
        return new ResultSet([], '', '', 10);
    }
}
