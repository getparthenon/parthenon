<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
