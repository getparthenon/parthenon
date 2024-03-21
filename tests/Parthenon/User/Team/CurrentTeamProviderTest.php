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
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Exception\CurrentUserNotATeamMemberException;
use Parthenon\User\Repository\TeamRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentTeamProviderTest extends TestCase
{
    public function testCallsTeam()
    {
        $security = $this->createMock(Security::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $user = new class() extends User implements MemberInterface {
            public function setTeam(TeamInterface $team): MemberInterface
            {
                // TODO: Implement setTeam() method.
            }

            public function getTeam(): TeamInterface
            {
                // TODO: Implement getTeam() method.
            }
        };
        $team = new Team();

        $security->method('getUser')->will($this->returnValue($user));
        $teamRepository->method('getByMember')->with($this->equalTo($user))->will($this->returnValue($team));

        $currentTeamProvider = new CurrentTeamProvider($security, $teamRepository);
        $actual = $currentTeamProvider->getCurrentTeam();

        $this->assertSame($team, $actual);
    }

    public function testUserNotMember()
    {
        $this->expectException(CurrentUserNotATeamMemberException::class);

        $security = $this->createMock(Security::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $user = new User();
        $team = new Team();

        $security->method('getUser')->will($this->returnValue($user));

        $currentTeamProvider = new CurrentTeamProvider($security, $teamRepository);
        $actual = $currentTeamProvider->getCurrentTeam();

        $this->assertSame($team, $actual);
    }
}
