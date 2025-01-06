<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class TeamCreatorTest extends TestCase
{
    public function testSaveTeam()
    {
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $user = $this->createMock(MemberInterface::class);
        $user->expects($this->once())->method('setTeam')->with($this->isInstanceOf(TeamInterface::class));
        $user->method('getEmail')->willReturn('test@example.org');

        $teamRepository->expects($this->once())->method('save')->with($this->isInstanceOf(TeamInterface::class));
        $userRepository->expects($this->once())->method('save')->with($this->equalTo($user));

        $teamCreator = new TeamCreator($teamRepository, $userRepository, new Team());
        $teamCreator->createForUser($user);
    }
}
