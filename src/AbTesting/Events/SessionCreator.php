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

namespace Parthenon\AbTesting\Events;

use Parthenon\AbTesting\Decider\EnabledDecider\DecidedManagerInterface;
use Parthenon\AbTesting\Repository\SessionRepositoryInterface;
use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class SessionCreator implements EventSubscriberInterface
{
    public const SESSION_ID = 'parthenon_ab_session_id';

    private SessionRepositoryInterface $sessionRepository;
    private DecidedManagerInterface $enabledDecider;

    public function __construct(SessionRepositoryInterface $sessionRepository, DecidedManagerInterface $enabledDecider)
    {
        $this->sessionRepository = $sessionRepository;
        $this->enabledDecider = $enabledDecider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $session = $event->getRequest()->getSession();

        if (!$this->enabledDecider->isTestable()) {
            return;
        }

        if (!$session->has(static::SESSION_ID)) {
            $request = $event->getRequest();
            $userAgent = $request->headers->get('User-Agent', 'No-User-Agent-Given');
            $userAgent = substr($userAgent, 0, 250);
            $uuid = $this->sessionRepository->createSession($userAgent, (string) $request->getClientIp());
            $session->set(static::SESSION_ID, (string) $uuid);
        }
    }

    public function onInteractiveLogin(InteractiveLoginEvent $authenticationEvent)
    {
        if (!$this->enabledDecider->isTestable()) {
            return;
        }

        $user = $authenticationEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        $session = $authenticationEvent->getRequest()->getSession();
        $sessionId = $session->get(static::SESSION_ID);
        $uuid = Uuid::fromString($sessionId);
        $this->sessionRepository->attachUserToSession($uuid, $user);
    }
}
