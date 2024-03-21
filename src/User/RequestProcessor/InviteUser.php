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
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostInviteEvent;
use Parthenon\User\Event\PreInviteEvent;
use Parthenon\User\Factory\EntityFactory;
use Parthenon\User\Form\Type\UserInviteType;
use Parthenon\User\Notification\MessageFactory;
use Parthenon\User\Repository\InviteCodeRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class InviteUser
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private Security $security,
        private InviteCodeRepositoryInterface $inviteCodeRepository,
        private EmailSenderInterface $sender,
        private UserInviteType $userInviteType,
        private EventDispatcherInterface $dispatcher,
        private MessageFactory $messageFactory,
        private EntityFactory $entityFactory,
        private RequestHandlerManagerInterface $requestHandlerManager,
        private string $defaultRole,
    ) {
    }

    public function process(Request $request)
    {
        $output = [];
        $formType = $this->formFactory->create(get_class($this->userInviteType));

        $requestHandler = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHandler->handleForm($formType, $request);
            if ($formType->isSubmitted() && $formType->isValid()) {
                $email = $formType->getData()['email'];
                $role = $formType->getData()['role'] ?? $this->defaultRole;
                $output['processed'] = true;
                $user = $this->security->getUser();
                if (!$user instanceof UserInterface) {
                    throw new \InvalidArgumentException('Not a user');
                }
                $inviteCode = $this->entityFactory->buildInviteCode($user, $email, $role);

                $this->dispatcher->dispatch(new PreInviteEvent($user, $inviteCode), PreInviteEvent::NAME);
                $this->inviteCodeRepository->save($inviteCode);
                $message = $this->messageFactory->getInviteMessage($user, $inviteCode);

                $this->sender->send($message);
                $this->dispatcher->dispatch(new PostInviteEvent($user, $inviteCode), PostInviteEvent::NAME);

                return $requestHandler->generateSuccessOutput($formType);
            } else {
                return $requestHandler->generateErrorOutput($formType);
            }
        }

        return $requestHandler->generateDefaultOutput($formType);
    }
}
