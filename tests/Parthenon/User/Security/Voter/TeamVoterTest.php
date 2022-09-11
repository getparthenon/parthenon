<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Security\Voter;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamOwnedInterface;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\TeamRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TeamVoterTest extends TestCase
{
    public function testDenysAccess()
    {
        $team = new Team();

        $item = new class($team) implements TeamOwnedInterface {
            private $team;

            public function __construct(TeamInterface $team)
            {
                $this->team = $team;
            }

            public function getOwningTeam(): TeamInterface
            {
                return $this->team;
            }
        };

        $member = new class() extends User {
        };

        $token = $this->createMock(UsernamePasswordToken::class);
        $token->method('getUser')->will($this->returnValue($member));

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $teamVoter = new TeamVoter($teamRepository);
        $actual = $teamVoter->vote($token, $item, ['vote']);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $actual);
    }

    public function testDenysIfNotAMemberOfAteam()
    {
        $member = $this->createMock(MemberInterface::class);
        $team = $this->createMock(TeamInterface::class);

        $item = new class($team) implements TeamOwnedInterface {
            private $team;

            public function __construct(TeamInterface $team)
            {
                $this->team = $team;
            }

            public function getOwningTeam(): TeamInterface
            {
                return $this->team;
            }
        };

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $id = 144;

        $team->method('getId')->will($this->returnValue($id));
        $team->method('hasMember')->with($this->equalTo($member))->will($this->returnValue(false));

        $teamRepository->method('findById')->with($this->equalTo($id))->will($this->returnValue($team));

        $token = $this->createMock(UsernamePasswordToken::class);
        $token->method('getUser')->will($this->returnValue($member));

        $teamVoter = new TeamVoter($teamRepository);
        $actual = $teamVoter->vote($token, $item, ['vote']);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $actual);
    }

    public function testAllowsIfAMemberOfAteam()
    {
        $member = $this->createMock(MemberInterface::class);
        $team = $this->createMock(TeamInterface::class);

        $item = new class($team) implements TeamOwnedInterface {
            private $team;

            public function __construct(TeamInterface $team)
            {
                $this->team = $team;
            }

            public function getOwningTeam(): TeamInterface
            {
                return $this->team;
            }
        };

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $id = 144;

        $team->method('getId')->will($this->returnValue($id));
        $team->method('hasMember')->with($this->equalTo($member))->will($this->returnValue(true));

        $teamRepository->method('findById')->with($this->equalTo($id))->will($this->returnValue($team));

        $token = $this->createMock(UsernamePasswordToken::class);
        $token->method('getUser')->will($this->returnValue($member));

        $teamVoter = new TeamVoter($teamRepository);
        $actual = $teamVoter->vote($token, $item, ['vote']);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $actual);
    }
}
