<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

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
