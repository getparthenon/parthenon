<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
