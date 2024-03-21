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

class DeleterTest extends TestCase
{
    public function testReturnsTrueIfAllVotesAreTrue()
    {
        $user = new User();

        $deleterOne = $this->createMock(DeleterInterface::class);
        $deleterTwo = $this->createMock(DeleterInterface::class);

        $deleterOne->expects($this->once())->method('delete')->with($this->equalTo($user))->willReturn(true);
        $deleterTwo->expects($this->once())->method('delete')->with($this->equalTo($user))->willReturn(true);

        $deleter = new Deleter();
        $deleter->add($deleterOne)->add($deleterTwo);

        $deleter->delete($user);
    }
}
