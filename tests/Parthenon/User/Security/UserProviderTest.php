<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Security;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserProviderTest extends TestCase
{
    public const EMAIL = 'iain@example.org';

    public function testReturnUser()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $user = new User();

        $userRepository->method('findByEmail')->with($this->equalTo(self::EMAIL))->will($this->returnValue($user));

        $userProvider = new UserProvider($userRepository);
        $actual = $userProvider->loadUserByIdentifier(self::EMAIL);

        $this->assertSame($user, $actual);
    }

    public function testThrowsException()
    {
        $this->expectException(UserNotFoundException::class);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $user = new User();

        $userRepository->method('findByEmail')->with($this->equalTo(self::EMAIL))->will($this->throwException(new NoEntityFoundException()));

        $userProvider = new UserProvider($userRepository);
        $userProvider->loadUserByIdentifier(self::EMAIL);
    }
}
