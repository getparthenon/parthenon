<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
