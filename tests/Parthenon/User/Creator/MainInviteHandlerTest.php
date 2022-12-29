<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
