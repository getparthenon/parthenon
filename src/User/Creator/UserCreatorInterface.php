<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Creator;

use Parthenon\User\Entity\UserInterface;

interface UserCreatorInterface
{
    public function create(UserInterface $user): void;
}
