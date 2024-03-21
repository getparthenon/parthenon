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

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\InvitedUserSignedUpEvent;
use Parthenon\User\Repository\InviteCodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InviteHandler implements InviteHandlerInterface
{
    public function __construct(
        private InviteCodeRepositoryInterface $inviteCodeRepository,
        private EventDispatcherInterface $dispatcher,
        private bool $enabled,
    ) {
    }

    public function supports(string $inviteCode): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $invite = $this->inviteCodeRepository->findActiveByCode($inviteCode);

            return true;
        } catch (NoEntityFoundException $e) {
        }

        return false;
    }

    public function handleInvite(UserInterface $user, string $inviteCode): void
    {
        $invite = $this->inviteCodeRepository->findActiveByCode($inviteCode);
        $invite->setUsed(true);
        $invite->setUsedAt(new \DateTime('now'));
        $invite->setInvitedUser($user);
        if ($invite->getRole()) {
            $user->setRoles([$invite->getRole()]);
        }
        $this->inviteCodeRepository->save($invite);
        $this->dispatcher->dispatch(new InvitedUserSignedUpEvent($user, $invite), InvitedUserSignedUpEvent::NAME);
    }
}
