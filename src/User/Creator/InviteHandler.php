<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
        $this->inviteCodeRepository->save($invite);
        $this->dispatcher->dispatch(new InvitedUserSignedUpEvent($user, $invite), InvitedUserSignedUpEvent::NAME);
    }
}
