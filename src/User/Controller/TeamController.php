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

namespace Parthenon\User\Controller;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Entity\User;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Parthenon\User\RequestProcessor\TeamInvite;
use Parthenon\User\Team\CurrentTeamProvider;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TeamController
{
    /**
     * @Route("/user/team", name="parthenon_team_view", methods={"GET"})
     */
    public function viewTeam(
        LoggerInterface $logger,
        Security $security,
        CurrentTeamProvider $teamProvider,
        TeamInviteCodeRepositoryInterface $inviteCodeRepository
    ) {
        $logger->info('A user viewed their team');
        /** @var User $user */
        $user = $security->getUser();
        $team = $teamProvider->getCurrentTeam();

        $sentInvites = [];
        foreach ($inviteCodeRepository->findAllUnusedInvitesForTeam($team) as $inviteCode) {
            $sentInvites[] = [
                'id' => (string) $inviteCode->getId(),
                'email' => $inviteCode->getEmail(),
                'created_at' => $inviteCode->getCreatedAt()->format(\DATE_ATOM),
            ];
        }

        $members = [];
        foreach ($team->getMembers() as $member) {
            $members[] = [
                'id' => (string) $member->getId(),
                'email' => $member->getEmail(),
                'name' => $member->getName(),
                'created_at' => $member->getCreatedAt()->format(\DATE_ATOM),
                'is_deleted' => $member->isDeleted(),
            ];
        }

        $body = [
            'sent_invites' => $sentInvites,
            'members' => $members,
        ];

        return new JsonResponse($body);
    }

    #[Route('/user/team/invite', name: 'parthenon_user_team_invite')]
    #[Template('user/team_invite.html.twig')]
    public function inviteUser(Request $request, TeamInvite $processor, LoggerInterface $logger)
    {
        $logger->info('A user has visited the invite page');

        return $processor->process($request);
    }

    #[Route('/user/team/invite/{id}/cancel', name: 'parthenon_team_invite_cancel', methods: ['POST'])]
    public function cancelInvite(Request $request, LoggerInterface $logger, TeamInviteCodeRepositoryInterface $inviteCodeRepository)
    {
        $logger->info('A user has cancelled an invite', ['invite_code_id' => $request->get('id')]);

        try {
            /** @var TeamInviteCode $inviteCode */
            $inviteCode = $inviteCodeRepository->findById($request->get('id'));

            $inviteCode->setUsed(true);
            $inviteCode->setUsedAt(new \DateTime('now'));
            $inviteCode->setCancelled(true);

            $inviteCodeRepository->save($inviteCode);
        } catch (\Throwable $e) {
            $logger->error('An error occurred while sending an invite', ['error_message' => $e->getMessage()]);

            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/user/team/member/{id}/disable', name: 'parthenon_team_invite_disable', methods: ['POST'])]
    public function disableMember(Request $request, LoggerInterface $logger, Security $security, CurrentTeamProvider $teamProvider, UserRepositoryInterface $userRepository)
    {
        $id = $request->get('id');
        $logger->info('A user has disable a member', ['id' => $id]);
        /** @var User $user */
        $user = $security->getUser();
        $team = $teamProvider->getCurrentTeam();

        try {
            if ($user->getId()->toString() == $id) {
                $logger->warning('A user has tried disable themselves', ['id' => $id]);

                return new JsonResponse(['success' => false]);
            }

            /** @var UserInterface $deletedUser */
            $deletedUser = $userRepository->findById($id);

            if (!$deletedUser instanceof MemberInterface) {
                throw new \Exception('Not a member');
            }
            if ($deletedUser->getTeam()->getId()->toString() != $team->getId()->toString()) {
                $logger->warning('A user has tried disable a user from a different team', ['id' => $id]);

                return new JsonResponse(['success' => false]);
            }

            $deletedUser->markAsDeleted();  /* @phpstan-ignore-line */
            $userRepository->save($deletedUser);
        } catch (\Throwable $e) {
            $logger->error('An error occured while trying to disable a user', ['error_message' => $e->getMessage()]);

            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse(['success' => true]);
    }
}
