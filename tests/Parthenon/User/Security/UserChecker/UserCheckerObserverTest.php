<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
