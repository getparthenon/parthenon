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
use Parthenon\User\Creator\UserCreator;
use Parthenon\User\Form\Type\UserSignUpType;
use Parthenon\User\Formatter\UserFormatterInterface;
use Parthenon\User\Security\LogUserInInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UserSignup
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private UserCreator $userCreator,
        private UserSignUpType $signUpType,
        private RequestHandlerManagerInterface $requestHandlerManager,
        private LogUserInInterface $logUserIn,
        private UserFormatterInterface $userFormatter,
        private bool $selfSignupEnabled,
        private bool $loggedInAfterSignup,
    ) {
    }

    public function process(Request $request)
    {
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        $formType = $this->formFactory->create(get_class($this->signUpType));

        if ($request->isMethod('POST')) {
            $inviteCode = $request?->get('code', null);

            if (!$this->selfSignupEnabled && is_null($inviteCode)) {
                $this->getLogger()->warning('A user sign up failed due not having an invite code while self sign up is disabled');

                return $requestHandler->generateErrorOutput($formType);
            }

            $requestHandler->handleForm($formType, $request);

            if ($formType->isSubmitted() && $formType->isValid()) {
                $user = $formType->getData();

                $this->userCreator->create($user);

                $this->getLogger()->info('A user has signed up successfully');

                $extraData = [];
                if ($this->loggedInAfterSignup) {
                    $this->logUserIn->login($user);
                    $extraData['user'] = $this->userFormatter->format($user);
                }

                return $requestHandler->generateSuccessOutput($formType, $extraData);
            } else {
                $this->getLogger()->info('A user sign up failed due to form validation');

                return $requestHandler->generateErrorOutput($formType);
            }
        }

        return $requestHandler->generateDefaultOutput($formType);
    }
}
