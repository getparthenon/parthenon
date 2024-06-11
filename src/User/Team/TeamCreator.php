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
        $team->setBillingEmail($user->getEmail());

        $this->teamRepository->save($team);
        $user->setTeam($team);
        $this->userRepository->save($user);
    }
}
