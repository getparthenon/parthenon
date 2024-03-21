<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
