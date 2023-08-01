<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
