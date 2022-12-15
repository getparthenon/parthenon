<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
