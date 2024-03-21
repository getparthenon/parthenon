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

namespace Parthenon\User\Security;

use Parthenon\User\Entity\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class LogUserIn implements LogUserInInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private string $firewallName,
    ) {
    }

    public function login(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $this->firewallName, $user->getRoles());
        $this->tokenStorage->setToken($token);

        $request = $this->requestStack->getCurrentRequest();
        $event = new InteractiveLoginEvent($request, $token);
        $this->dispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);
    }
}
