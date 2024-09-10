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

namespace Parthenon\User\RequestProcessor;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Event\PostUserConfirmEvent;
use Parthenon\User\Event\PreUserConfirmEvent;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfirmEmail
{
    use LoggerAwareTrait;

    /**
     * ConfirmEmail constructor.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $dispatcher,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        try {
            $user = $this->userRepository->findByConfirmationCode($request->get('confirmationCode'));
        } catch (NoEntityFoundException $e) {
            return $requestHandler->generateErrorOutput(null);
        }

        $this->dispatcher->dispatch(new PreUserConfirmEvent($user), PreUserConfirmEvent::NAME);
        $user->setIsConfirmed(true);
        $user->setActivatedAt(new \DateTime('now'));
        $this->dispatcher->dispatch(new PostUserConfirmEvent($user), PostUserConfirmEvent::NAME);
        $this->userRepository->save($user);
        $this->getLogger()->info('A user has confirmed their email', ['email' => $user->getEmail()]);

        return $requestHandler->generateSuccessOutput(null);
    }
}
