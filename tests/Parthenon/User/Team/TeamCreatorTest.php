<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

        $teamRepository->expects($this->once())->method('save')->with($this->isInstanceOf(TeamInterface::class));
        $userRepository->expects($this->once())->method('save')->with($this->equalTo($user));

        $teamCreator = new TeamCreator($teamRepository, $userRepository, new Team());
        $teamCreator->createForUser($user);
    }
}
