<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\MemberInterface;

interface TeamCreatorInterface
{
    public function createForUser(MemberInterface $user): void;
}
