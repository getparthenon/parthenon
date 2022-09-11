<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
