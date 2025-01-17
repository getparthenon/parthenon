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
