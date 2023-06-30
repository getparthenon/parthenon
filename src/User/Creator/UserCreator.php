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

use Parthenon\Notification\EmailSenderInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostUserSignupEvent;
use Parthenon\User\Event\PreUserSignupEvent;
use Parthenon\User\Notification\MessageFactory;
use Parthenon\User\Repository\UserRepositoryInterface;
use Parthenon\User\Team\TeamCreator;
use Parthenon\User\Team\TeamCreatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final class UserCreator implements UserCreatorInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherFactoryInterface $encoderFactory,
        private EventDispatcherInterface $eventDispatcher,
        private MainInviteHandlerInterface $inviteHandler,
        private EmailSenderInterface $emailSender,
        private MessageFactory $messageFactory,
        private bool $teamCreation,
        private TeamCreatorInterface $teamCreator,
        private RequestStack $requestStack,
        private string $defaultRole,
        private bool $emailConfirmation,
    ) {
    }

    public function create(UserInterface $user): void
    {
        $this->eventDispatcher->dispatch(new PreUserSignupEvent($user), PreUserSignupEvent::NAME);

        $encoder = $this->encoderFactory->getPasswordHasher($user);
        $user->setPassword($encoder->hash($user->getPassword()));
        $user->setConfirmationCode(bin2hex(random_bytes(32)));
        $user->setCreatedAt(new \DateTime('now'));
        $user->setRoles([$this->defaultRole]);
        $this->repository->save($user);

        if ($this->teamCreation) { // TODO move to teamCreator
            $this->teamCreator->createForUser($user);
        }

        $request = $this->requestStack->getMainRequest();
        $inviteCode = $request?->get('code', null);
        if ($inviteCode) {
            $this->inviteHandler->handleInvite($user, $inviteCode);
            $user->setIsConfirmed(true);
            $this->repository->save($user);
        } else {
            $message = $this->messageFactory->getUserSignUpMessage($user);
            $this->emailSender->send($message);
            $user->setIsConfirmed(!$this->emailConfirmation);
            $this->repository->save($user);
        }

        $this->eventDispatcher->dispatch(new PostUserSignupEvent($user), PostUserSignupEvent::NAME);
    }
}
