<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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
