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

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\RequestProcessor\AlreadyInvitedException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\RequestHandler\RequestHandlerManagerInterface;
use Parthenon\Notification\EmailSenderInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Event\PostTeamInviteEvent;
use Parthenon\User\Event\PreTeamInviteEvent;
use Parthenon\User\Factory\EntityFactory;
use Parthenon\User\Form\Type\UserInviteType;
use Parthenon\User\Notification\MessageFactory;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TeamInvite
{
    use LoggerAwareTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private Security $security,
        private TeamInviteCodeRepositoryInterface $inviteCodeRepository,
        private EmailSenderInterface $sender,
        private UserInviteType $userInviteType,
        private EventDispatcherInterface $dispatcher,
        private MessageFactory $messageFactory,
        private EntityFactory $entityFactory,
        private TeamRepositoryInterface $teamRepository,
        private RequestStack $requestStack,
        private RequestHandlerManagerInterface $requestHandlerManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private UserRepositoryInterface $userRepository,
        private string $defaultRole,
        private array $roles,
    ) {
    }

    public function process(Request $request)
    {
        $output = ['success' => false, 'already_invited' => false, 'already_a_member' => false, 'hit_limit' => false];

        $formType = $this->formFactory->create(get_class($this->userInviteType));
        $requestHander = $this->requestHandlerManager->getRequestHandler($request);

        if ($request->isMethod('POST')) {
            $requestHander->handleForm($formType, $request);
            if ($formType->isSubmitted() && $formType->isValid()) {
                $email = $formType->getData()['email'];
                $role = $formType->getData()['role'] ?? $this->defaultRole;
                $output['processed'] = true;
                try {
                    $user = $this->security->getUser();
                    if (!$user instanceof UserInterface) {
                        throw new \InvalidArgumentException('Not a user');
                    }

                    if (!$user instanceof MemberInterface) {
                        $this->getLogger()->critical('A user tried to send a team invite when not a member of a team');
                        throw new \InvalidArgumentException('Not a member of a team');
                    }
                    $team = $this->teamRepository->getByMember($user);

                    if ($this->inviteCodeRepository->hasInviteForEmailAndTeam($email, $team)) {
                        throw new AlreadyInvitedException();
                    }

                    if (!in_array($role, $this->roles) && $this->defaultRole !== $role) {
                        $this->getLogger()->error('A team invite was not sent due an invalid role being used', ['role' => $role]);
                        throw new GeneralException('Invalid role');
                    }

                    $inviteCode = $this->entityFactory->buildTeamInviteCode($user, $team, $email, $role);
                    if (!$this->authorizationChecker->isGranted(TeamInviteCode::AUTH_CHECKER_ATTRIBUTE, $inviteCode)) {
                        $output['success'] = false;
                        $output['hit_limit'] = true;
                        $this->getLogger()->info('A team invite was not sent due to plan limits');

                        return $requestHander->generateErrorOutput($formType, $output);
                    }

                    $this->dispatcher->dispatch(new PreTeamInviteEvent($user, $team, $inviteCode), PreTeamInviteEvent::NAME);
                    $this->inviteCodeRepository->save($inviteCode);
                    $message = $this->messageFactory->getTeamInviteMessage($user, $team, $inviteCode);

                    $this->sender->send($message);
                    $this->dispatcher->dispatch(new PostTeamInviteEvent($user, $team, $inviteCode), PostTeamInviteEvent::NAME);
                    $output['success'] = true;
                    $this->requestStack->getSession()->getFlashBag()->add('success', 'parthenon.user.team_invite.success');
                    $this->getLogger()->info('A team invite was successfully sent');
                } catch (AlreadyInvitedException $e) {
                    $this->getLogger()->info('A team invite failed to be created due to one already existing');

                    $this->requestStack->getSession()->getFlashBag()->add('error', 'parthenon.user.team_invite.already_invited');
                    $output['already_invited'] = true;

                    return $requestHander->generateErrorOutput($formType, $output);
                }
            }
        }

        $output['form'] = $formType->createView();

        return $requestHander->generateDefaultOutput($formType, $output);
    }
}
