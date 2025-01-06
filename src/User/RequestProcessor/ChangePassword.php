<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Event\PostPasswordChangeEvent;
use Parthenon\User\Event\PrePasswordChangeEvent;
use Parthenon\User\Form\Type\ChangePasswordType;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

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
