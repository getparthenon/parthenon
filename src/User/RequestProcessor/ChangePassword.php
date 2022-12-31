<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\RequestProcessor;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Event\PostPasswordChangeEvent;
use Parthenon\User\Event\PrePasswordChangeEvent;
use Parthenon\User\Form\Type\ChangePasswordType;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class ChangePassword
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private UserRepositoryInterface $userRepository,
        private ChangePasswordType $changePasswordType,
        private EventDispatcherInterface $dispatcher,
        private PasswordHasherFactoryInterface $encoderFactory,
        private Security $security,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $formType = $this->formFactory->create(get_class($this->changePasswordType));
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHandler->handleForm($formType, $request);

            if ($formType->isSubmitted() && $formType->isValid()) {
                $newPassword = $formType->getData()['new_password'];

                /** @var \Parthenon\User\Entity\UserInterface $user */
                $user = $this->security->getUser();
                $this->dispatcher->dispatch(new PrePasswordChangeEvent($user), PrePasswordChangeEvent::NAME);
                $user->setPassword($this->encoderFactory->getPasswordHasher($user)->hash($newPassword));
                $this->userRepository->save($user);
                $this->dispatcher->dispatch(new PostPasswordChangeEvent($user), PostPasswordChangeEvent::NAME);

                return $requestHandler->generateSuccessOutput($formType);
            } else {
                return $requestHandler->generateErrorOutput($formType);
            }
        }

        return $requestHandler->generateDefaultOutput($formType);
    }
}
