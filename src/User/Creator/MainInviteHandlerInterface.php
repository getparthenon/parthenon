<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Creator;

use Parthenon\User\Entity\UserInterface;

interface MainInviteHandlerInterface
{
    public function add(InviteHandlerInterface $inviteHandler);

    public function handleInvite(UserInterface $user, string $inviteCode): void;
}
