<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class DeciderTest extends TestCase
{
    public function testReturnsTrueIfAllVotesAreTrue()
    {
        $user = new User();

        $voterOne = $this->createMock(VoterInterface::class);
        $voterTwo = $this->createMock(VoterInterface::class);

        $voterOne->method('canDelete')->with($this->equalTo($user))->willReturn(true);
        $voterTwo->method('canDelete')->with($this->equalTo($user))->willReturn(true);

        $decider = new Decider();
        $decider->add($voterOne)->add($voterTwo);

        $canDelete = $decider->canDelete($user);

        $this->assertTrue($canDelete);
    }

    public function testReturnsFalseIfOneVoteIsFalse()
    {
        $user = new User();

        $voterOne = $this->createMock(VoterInterface::class);
        $voterTwo = $this->createMock(VoterInterface::class);

        $voterOne->method('canDelete')->with($this->equalTo($user))->willReturn(false);
        $voterTwo->method('canDelete')->with($this->equalTo($user))->willReturn(true);

        $decider = new Decider();
        $decider->add($voterOne)->add($voterTwo);

        $canDelete = $decider->canDelete($user);

        $this->assertFalse($canDelete);
    }
}
