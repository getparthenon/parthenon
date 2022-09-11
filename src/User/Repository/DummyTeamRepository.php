<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
