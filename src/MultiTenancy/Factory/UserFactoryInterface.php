<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\MultiTenancy\Factory;

use Parthenon\MultiTenancy\Model\SignUp;
use Parthenon\User\Entity\UserInterface;

interface UserFactoryInterface
{
    public function buildUserFromSignUp(SignUp $signUp): UserInterface;
}
