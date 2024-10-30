<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\User\RequestProcessor;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\User\Event\PostPasswordResetEvent;
use Parthenon\User\Event\PrePasswordResetEvent;
use Parthenon\User\Factory\EntityFactory;
use Parthenon\User\Form\Type\PasswordResetType;
use Parthenon\User\Notification\MessageFactory;
use Parthenon\User\Repository\ForgotPasswordCodeRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordReset
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private UserRepositoryInterface $userRepository,
        private ForgotPasswordCodeRepositoryInterface $passwordResetRepository,
        private EmailSenderInterface $sender,
        private PasswordResetType $passwordResetType,
        private EventDispatcherInterface $dispatcher,
        private MessageFactory $messageFactory,
        private EntityFactory $factory,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $output = [
            'processed' => false,
        ];
        $formType = $this->formFactory->create(get_class($this->passwordResetType));

        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHandler->handleForm($formType, $request);
            if ($formType->isSubmitted() && $formType->isValid()) {
                $email = $formType->getData()['email'];
                $output['processed'] = true;
                try {
                    $user = $this->userRepository->findByEmail($email);
                    $passwordReset = $this->factory->buildPasswordReset($user);

                    $this->dispatcher->dispatch(new PrePasswordResetEvent($user, $passwordReset), PrePasswordResetEvent::NAME);

                    $this->passwordResetRepository->save($passwordReset);

                    $message = $this->messageFactory->getPasswordResetMessage($user, $passwordReset);

                    $this->sender->send($message);
                    $this->dispatcher->dispatch(new PostPasswordResetEvent($user, $passwordReset), PostPasswordResetEvent::NAME);
                } catch (NoEntityFoundException $e) {
                    $this->getLogger()->warning("Attempt to reset password for an account that doesn't exist");
                }

                return $requestHandler->generateSuccessOutput($formType, $output);
            } else {
                return $requestHandler->generateErrorOutput($formType, $output);
            }
        }

        return $requestHandler->generateDefaultOutput($formType, $output);
    }
}
