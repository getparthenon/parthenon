<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Model\SignUp;
use Parthenon\User\Entity\User;
use PHPUnit\Framework\TestCase;

class UserFactoryTest extends TestCase
{
    public function testReturnsUser()
    {
        $userFactory = new UserFactory(new User());

        $signUp = new SignUp();
        $signUp->setEmail('user@example.org');
        $signUp->setPassword('a-fake-password');

        $user = $userFactory->buildUserFromSignUp($signUp);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user@example.org', $user->getEmail());
        $this->assertEquals('a-fake-password', $user->getPassword());
    }
}
