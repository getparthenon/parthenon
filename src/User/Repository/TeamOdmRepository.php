<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
