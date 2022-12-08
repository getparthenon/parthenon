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
