<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

class TeamRepository extends DoctrineCrudRepository implements TeamRepositoryInterface
{
    public function getByMember(MemberInterface $member): TeamInterface
    {
        $team = $member->getTeam();
        $this->entityRepository->getEntityManager()->refresh($team);

        return $team;
    }
}
