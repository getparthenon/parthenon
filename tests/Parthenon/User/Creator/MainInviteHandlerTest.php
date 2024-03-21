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

namespace Parthenon\User\Creator;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class MainInviteHandlerTest extends TestCase
{
    public const CODE = 'code';

    public function testSupportsCallHandler()
    {
        $inviteHandler = $this->createMock(InviteHandlerInterface::class);
        $inviteHandler->method('supports')->with($this->equalTo(self::CODE))->will($this->returnValue(true));
        $inviteHandler->expects($this->once())->method('handleInvite')->with($this->isInstanceOf(User::class), $this->equalTo(self::CODE));

        $user = new User();

        $mainInviteHandler = new MainInviteHandler();
        $mainInviteHandler->add($inviteHandler);
        $mainInviteHandler->handleInvite($user, self::CODE);
    }

    public function testDoesntSupportsCallHandler()
    {
        $inviteHandler = $this->createMock(InviteHandlerInterface::class);
        $inviteHandler->method('supports')->with($this->equalTo(self::CODE))->will($this->returnValue(false));
        $inviteHandler->expects($this->never())->method('handleInvite')->with($this->isInstanceOf(User::class), $this->equalTo(self::CODE));

        $user = new User();

        $mainInviteHandler = new MainInviteHandler();
        $mainInviteHandler->add($inviteHandler);
        $mainInviteHandler->handleInvite($user, self::CODE);
    }
}
