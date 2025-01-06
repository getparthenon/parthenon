<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\User\Creator;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\User;
use Parthenon\User\Event\InvitedUserSignedUpEvent;
use Parthenon\User\Repository\InviteCodeRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InviteHandlerTest extends TestCase
{
    public const CODE = 'code';

    public function testAssignUsersToInvite()
    {
        $inviteCodeRepository = $this->createMock(InviteCodeRepositoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $inviteCode = $this->createMock(InviteCode::class);

        $user = new User();

        $inviteCodeRepository->method('findActiveByCode')->with($this->equalTo(self::CODE))->will($this->returnValue($inviteCode));

        $inviteCode->expects($this->once())->method('setInvitedUser')->with($this->equalTo($user));
        $inviteCode->expects($this->once())->method('setUsed')->with($this->equalTo(true));
        $inviteCode->expects($this->once())->method('setUsedAt')->with($this->isInstanceOf(\DateTime::class))->will($this->returnSelf());

        $eventDispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(InvitedUserSignedUpEvent::class), InvitedUserSignedUpEvent::NAME);

        $inviteHandler = new InviteHandler($inviteCodeRepository, $eventDispatcher, true);
        $inviteHandler->handleInvite($user, self::CODE);
    }
}
