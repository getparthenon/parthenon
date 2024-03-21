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

namespace Parthenon\User\Repository;

use Parthenon\Athena\ResultSet;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

class DummyTeamRepository implements TeamRepositoryInterface
{
    public function findById($id)
    {
        // TODO: Implement findById() method.
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function getByMember(MemberInterface $member): TeamInterface
    {
        return new class() implements TeamInterface {
            public function getId()
            {
                return null;
            }

            public function addMember(MemberInterface $member): TeamInterface
            {
                return $this;
            }

            public function hasMember(MemberInterface $member): bool
            {
                return false;
            }

            public function getMembers(): array
            {
                return [];
            }

            public function getTeamSize(): int
            {
                return 0;
            }

            public function setCreatedAt(\DateTime $createdAt): TeamInterface
            {
                return $this;
            }

            public function setName(?string $name)
            {
                // TODO: Implement setName() method.
            }

            public function getName(): ?string
            {
                // TODO: Implement getName() method.
            }
        };
    }

    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet
    {
        return new ResultSet([], $sortKey, $sortType, $limit);
    }

    public function getById($id, $includeDeleted = false)
    {
        // TODO: Implement getById() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    public function undelete($entity)
    {
        // TODO: Implement undelete() method.
    }
}
