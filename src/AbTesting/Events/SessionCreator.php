<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
