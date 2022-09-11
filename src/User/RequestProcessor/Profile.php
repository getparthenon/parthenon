<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\RequestProcessor;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostProfileEvent;
use Parthenon\User\Event\PreProfileEvent;
use Parthenon\User\Form\Type\ProfileType;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class Profile
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private UserRepositoryInterface $userRepository,
        private ProfileType $profileType,
        private Security $security,
        private EventDispatcherInterface $dispatcher,
        private RequestHandlerManagerInterface $requestHandlerManager,
    ) {
    }

    public function process(Request $request)
    {
        $this->getLogger()->info('A user has view their profile');

        /** @var UserInterface $securityUser */
        $securityUser = $this->security->getUser();
        $user = $this->userRepository->findByEmail($securityUser->getEmail());
        $formType = $this->formFactory->create(get_class($this->profileType), $user);
        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHandler->handleForm($formType, $request);
            if ($formType->isSubmitted() && $formType->isValid()) {
                $this->getLogger()->info('A user has successfully submitted their profile');
                $user = $formType->getData();
                $this->dispatcher->dispatch(new PreProfileEvent($user), PreProfileEvent::NAME);
                $this->userRepository->save($user);
                $this->dispatcher->dispatch(new PostProfileEvent($user), PostProfileEvent::NAME);
            } else {
                $this->getLogger()->info('A user has submitted an invalid profile');

                return $requestHandler->generateErrorOutput($formType);
            }
        }

        return $requestHandler->generateDefaultOutput($formType);
    }
}
