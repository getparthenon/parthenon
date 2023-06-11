<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\RequestProcessor;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\JsonRequestHandler;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostPasswordResetConfirmEvent;
use Parthenon\User\Event\PostUserConfirmEvent;
use Parthenon\User\Event\PrePasswordResetConfirmEvent;
use Parthenon\User\Repository\ForgotPasswordCodeRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetConfirm
{
    use LoggerAwareTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ForgotPasswordCodeRepositoryInterface $forgotPasswordCodeRepository,
        private PasswordHasherFactoryInterface $encoderFactory,
        private EventDispatcherInterface $eventDispatcher,
        private UrlGeneratorInterface $urlGenerator,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        $code = $request->get('code');
        try {
            $passwordReset = $this->forgotPasswordCodeRepository->findActiveByCode($code);
        } catch (NoEntityFoundException $e) {
            return $requestHandler->generateErrorOutput(null);
        }

        if ($passwordReset->isUsed()) {
            $this->getLogger()->warning('A user has tried to reset their password with an used code', ['code' => $code]);

            return $requestHandler->generateErrorOutput(null);
        }

        if ($passwordReset->isExpired()) {
            $this->getLogger()->warning('A user has tried to reset their password with an expired code', ['code' => $code]);

            return $requestHandler->generateErrorOutput(null);
        }

        if (!$request->isMethod('POST')) {
            return $requestHandler->generateDefaultOutput(null);
        }

        $passwordReset->setUsed(true);
        $passwordReset->setUsedAt(new \DateTime('now'));
        /** @var UserInterface $user */
        $user = $this->userRepository->getById($passwordReset->getUserId());

        $this->eventDispatcher->dispatch(new PrePasswordResetConfirmEvent($user), PrePasswordResetConfirmEvent::NAME);

        if ($requestHandler instanceof JsonRequestHandler) {
            $json = json_decode($request->getContent(), true);
            $newPassword = $json['password'];
        } else {
            $newPassword = $request->get('password');
        }
        $newPasswordHash = $this->encoderFactory->getPasswordHasher($user)->hash($newPassword);

        $user->setPassword($newPasswordHash);
        $hasActivatedUser = false;
        if (!$user->isConfirmed()) {
            $user->setActivatedAt(new \DateTime('now'));
            $user->setIsConfirmed(true);
            $hasActivatedUser = true;
        }
        $this->userRepository->save($user);
        $this->forgotPasswordCodeRepository->save($passwordReset);

        if ($hasActivatedUser) {
            $this->eventDispatcher->dispatch(new PostUserConfirmEvent($user), PostUserConfirmEvent::NAME);
        }

        $this->eventDispatcher->dispatch(new PostPasswordResetConfirmEvent($user), PostPasswordResetConfirmEvent::NAME);
        $this->getLogger()->info('A user has reset their password', ['email' => $user->getEmail()]);

        if ($requestHandler instanceof JsonRequestHandler) {
            return new JsonResponse(['success' => true]);
        }

        return new RedirectResponse($this->urlGenerator->generate('parthenon_user_login'));
    }
}
