<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\User\Creator;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\TeamInvitedUserSignedUpEvent;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TeamInviteHandler implements InviteHandlerInterface
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private TeamInviteCodeRepositoryInterface $inviteCodeRepository,
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
        } catch (NoEntityFoundException $e) {
            return false;
        }

        return true;
    }

    public function handleInvite(UserInterface $user, string $inviteCode): void
    {
        $invite = $this->inviteCodeRepository->findActiveByCode($inviteCode);

        $team = $invite->getTeam();

        if (!$team instanceof TeamInterface) {
            throw new GeneralException('No team for invite');
        }

        if (!$user instanceof MemberInterface) {
            throw new GeneralException('User is not able to be a team member, must implement '.MemberInterface::class);
        }

        $team->addMember($user);
        $user->setTeam($team);

        if ($invite->hasRole()) {
            $user->setRoles([$invite->getRole()]);
        }

        $this->teamRepository->save($team);
        $invite->setUsed(true);
        $invite->setUsedAt(new \DateTime('now'));
        $invite->setInvitedUser($user);
        $this->inviteCodeRepository->save($invite);
        $this->dispatcher->dispatch(new TeamInvitedUserSignedUpEvent($user, $invite, $team), TeamInvitedUserSignedUpEvent::NAME);
    }
}
