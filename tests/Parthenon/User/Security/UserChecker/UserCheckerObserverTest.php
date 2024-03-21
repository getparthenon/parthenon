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

namespace Parthenon\User\Security\UserChecker;

use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserCheckerObserverTest extends TestCase
{
    public function testCallsPreAuth()
    {
        $checkerOne = $this->createMock(UserCheckerInterface::class);
        $checkerTwo = $this->createMock(UserCheckerInterface::class);
        $user = new User();

        $checkerOne->expects($this->once())->method('checkPreAuth')->with($this->equalTo($user));
        $checkerTwo->expects($this->once())->method('checkPreAuth')->with($this->equalTo($user));

        $userChecker = new UserCheckerObserver();
        $userChecker->add($checkerOne);
        $userChecker->add($checkerTwo);

        $userChecker->checkPreAuth($user);
    }

    public function testCallsPostAuth()
    {
        $checkerOne = $this->createMock(UserCheckerInterface::class);
        $checkerTwo = $this->createMock(UserCheckerInterface::class);
        $user = new User();

        $checkerOne->expects($this->once())->method('checkPostAuth')->with($this->equalTo($user));
        $checkerTwo->expects($this->once())->method('checkPostAuth')->with($this->equalTo($user));

        $userChecker = new UserCheckerObserver();
        $userChecker->add($checkerOne);
        $userChecker->add($checkerTwo);

        $userChecker->checkPostAuth($user);
    }
}
