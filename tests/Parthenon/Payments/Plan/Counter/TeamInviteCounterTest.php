<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan\Security\Counter;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Payments\Plan\Counter\TeamInviteCounter;
use Parthenon\Payments\Plan\LimitedUserInterface;
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
