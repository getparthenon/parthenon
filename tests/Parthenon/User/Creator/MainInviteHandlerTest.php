<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
