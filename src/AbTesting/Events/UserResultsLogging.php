<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Events;

use Parthenon\AbTesting\Experiment\ResultLogger;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostUserConfirmEvent;
use Parthenon\User\Event\PostUserSignupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class UserResultsLogging implements EventSubscriberInterface
{
    private ResultLogger $resultLogger;
    private bool $enabled;

    public function __construct(ResultLogger $resultLogger, bool $enabled)
    {
        $this->resultLogger = $resultLogger;
        $this->enabled = $enabled;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostUserSignupEvent::NAME => 'handleUserSignup',
            PostUserConfirmEvent::NAME => 'handleUserConfirm',
            SecurityEvents::INTERACTIVE_LOGIN => 'handleUserLogin',
        ];
    }

    public function handleUserLogin(InteractiveLoginEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $this->resultLogger->log('user_login', $user);
    }

    public function handleUserSignup(PostUserSignupEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }
        $this->resultLogger->log('user_signup', $event->getUser());
    }

    public function handleUserConfirm(PostUserConfirmEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->resultLogger->log('user_email_confirmed', $event->getUser());
    }
}
