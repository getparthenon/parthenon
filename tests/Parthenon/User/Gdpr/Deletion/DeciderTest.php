<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
