<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;

final class TeamCreator implements TeamCreatorInterface
{
    private TeamRepositoryInterface $teamRepository;
    private TeamInterface $teamClass;
    private UserRepositoryInterface $userRepository;

    public function __construct(TeamRepositoryInterface $teamRepository, UserRepositoryInterface $userRepository, TeamInterface $teamClass)
    {
        $this->teamRepository = $teamRepository;
        $this->teamClass = $teamClass;
        $this->userRepository = $userRepository;
    }

    public function createForUser(MemberInterface $user): void
    {
        $className = get_class($this->teamClass);
        /** @var TeamInterface $team */
        $team = new $className();
        $team->setName(sprintf("%s's team", $user->getEmail()));
        $team->setCreatedAt(new \DateTime('now'));
        $team->addMember($user);

        $this->teamRepository->save($team);
        $user->setTeam($team);
        $this->userRepository->save($user);
    }
}
