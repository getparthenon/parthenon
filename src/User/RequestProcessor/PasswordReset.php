<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
