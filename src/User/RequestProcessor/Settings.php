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

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostSettingsEvent;
use Parthenon\User\Event\PreSettingsEvent;
use Parthenon\User\Form\Type\SettingsType;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Settings
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private UserRepositoryInterface $userRepository,
        private SettingsType $settingsType,
        private Security $security,
        private EventDispatcherInterface $dispatcher,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $this->getLogger()->info('A user has view their settings');

        /** @var UserInterface $securityUser */
        $securityUser = $this->security->getUser();
        $user = $this->userRepository->findByEmail($securityUser->getEmail());
        $formType = $this->formFactory->create(get_class($this->settingsType), $user);
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHandler->handleForm($formType, $request);
            if ($formType->isSubmitted() && $formType->isValid()) {
                $this->getLogger()->info('A user has successfully submitted their settings');
                $user = $formType->getData();
                $this->dispatcher->dispatch(new PreSettingsEvent($user), PreSettingsEvent::NAME);
                $this->userRepository->save($user);
                $this->dispatcher->dispatch(new PostSettingsEvent($user), PostSettingsEvent::NAME);
            } else {
                $this->getLogger()->info('A user has submitted an invalid settings');

                return $requestHandler->generateErrorOutput($formType);
            }
        }

        return $requestHandler->generateDefaultOutput($formType);
    }
}
