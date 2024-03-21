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

use Parthenon\Athena\Repository\OdmCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

class TeamOdmRepository extends OdmCrudRepository implements TeamRepositoryInterface, ActiveMembersRepositoryInterface
{
    public function getByMember(MemberInterface $member): TeamInterface
    {
        $qb = $this->documentRepository->createQueryBuilder();

        $qb->field('members.id')->equals($member->getId());

        $team = $qb->getQuery()->getSingleResult();

        if (!$team instanceof TeamInterface) {
            throw new NoEntityFoundException();
        }

        return $team;
    }

    public function getCountForActiveTeamMemebers(TeamInterface $team): int
    {
        /** @var TeamInterface $team */
        $team = $this->getById($team->getId());

        $members = array_filter($team->getMembers(), function (MemberInterface $member) {
            return !$member->isDeleted();
        });

        return count($members);
    }

    public function getMembers(TeamInterface $team): array
    {
        /** @var TeamInterface $team */
        $team = $this->getById($team->getId());

        return $team->getMembers();
    }
}
