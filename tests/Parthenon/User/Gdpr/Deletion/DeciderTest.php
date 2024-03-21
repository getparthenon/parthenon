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
