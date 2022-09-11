<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
