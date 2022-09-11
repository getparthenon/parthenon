<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
