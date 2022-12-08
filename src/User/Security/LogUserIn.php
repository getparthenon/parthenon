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

namespace Parthenon\User\Security;

use Parthenon\User\Entity\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\SecurityEvents;

class LogUserIn implements LogUserInInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TokenStorageInterface $tokenStorage,
        private string $firewallName,
    ) {
    }

    public function login(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $this->firewallName, $user->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new SecurityEvents();
        $this->dispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);
    }
}
