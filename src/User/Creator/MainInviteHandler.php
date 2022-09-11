<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Creator;

use Parthenon\User\Entity\UserInterface;

final class MainInviteHandler implements MainInviteHandlerInterface
{
    /**
     * @var InviteHandlerInterface[]
     */
    private array $handlers = [];

    public function add(InviteHandlerInterface $inviteHandler)
    {
        $this->handlers[] = $inviteHandler;
    }

    public function handleInvite(UserInterface $user, string $inviteCode): void
    {
        foreach ($this->handlers as $inviteHandlerhandler) {
            if ($inviteHandlerhandler->supports($inviteCode)) {
                $inviteHandlerhandler->handleInvite($user, $inviteCode);

                return;
            }
        }
    }
}
