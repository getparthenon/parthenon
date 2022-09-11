<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Model\SignUp;
use Parthenon\User\Entity\UserInterface;

class UserFactory implements UserFactoryInterface
{
    public function __construct(private UserInterface $user)
    {
    }

    public function buildUserFromSignUp(SignUp $signUp): UserInterface
    {
        $className = get_class($this->user);

        /** @var UserInterface $user */
        $user = new $className();
        $user->setEmail($signUp->getEmail());
        $user->setName($signUp->getName());
        $user->setPassword($signUp->getPassword());

        return $user;
    }
}
