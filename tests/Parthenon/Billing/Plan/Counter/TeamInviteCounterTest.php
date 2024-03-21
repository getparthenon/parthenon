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

namespace Parthenon\Billing\Plan\Security\Counter;

use Parthenon\Billing\Plan\Counter\TeamInviteCounter;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\ActiveMembersRepositoryInterface;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use PHPUnit\Framework\TestCase;

class TeamInviteCounterTest extends TestCase
{
    public function testExceptionFlungWhenNotMember()
    {
        $this->expectException(GeneralException::class);

        $teamInviteRepository = $this->createMock(TeamInviteCodeRepositoryInterface::class);
        $activeMemberRepository = $this->createMock(ActiveMembersRepositoryInterface::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $user = new class() extends User implements LimitedUserInterface {
            public function getPlanName(): ?string
            {
                // TODO: Implement getPlanName() method.
            }
        };

        $subject = new TeamInviteCounter($teamInviteRepository, $activeMemberRepository, $teamRepository);
        $subject->getCount($user);
    }

    public function testGetCount()
    {
        $teamInviteRepository = $this->createMock(TeamInviteCodeRepositoryInterface::class);
        $activeMemberRepository = $this->createMock(ActiveMembersRepositoryInterface::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $user = new class() extends User implements LimitedUserInterface, MemberInterface {
            public function getPlanName(): ?string
            {
                // TODO: Implement getPlanName() method.
            }

            public function setTeam(TeamInterface $team): MemberInterface
            {
                // TODO: Implement setTeam() method.
            }

            public function getTeam(): TeamInterface
            {
                // TODO: Implement getTeam() method.
            }
        };
        $team = new class() extends Team {};

        $teamRepository->method('getByMember')->with($user)->willReturn($team);

        $teamInviteRepository->method('getUsableInviteCount')->with($team)->willReturn(10);
        $activeMemberRepository->method('getCountForActiveTeamMemebers')->with($team)->willReturn(5);

        $subject = new TeamInviteCounter($teamInviteRepository, $activeMemberRepository, $teamRepository);
        $actual = $subject->getCount($user);

        $this->assertEquals(15, $actual);
    }
}
