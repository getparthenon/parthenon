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
        private RequestHandlerManagerInterface $requestHandlerManager
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
