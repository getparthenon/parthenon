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

namespace Parthenon\User\Controller;

use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Gdpr\Export\ExportExecutorInterface;
use Parthenon\User\Repository\ActiveMembersRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AthenaController implements AthenaControllerInterface
{
    #[Route('/athena/user/{id}/gdpr/export', name: 'parthenon_athena_user_gdpr_export')]
    public function configure(Request $request, UserRepositoryInterface $userRepository, ExportExecutorInterface $exportExecutor): Response
    {
        /** @var UserInterface $user */
        $user = $userRepository->getById($request->get('id'));

        return $exportExecutor->export($user);
    }

    #[Route('/athena/team/{id}/members', name: 'parthenon_athena_user_team_members')]
    public function teamMembers(Request $request, TeamRepositoryInterface $teamRepository, ActiveMembersRepositoryInterface $activeMembersRepository, Environment $twig)
    {
        $team = $teamRepository->getById($request->get('id'));
        $members = $activeMembersRepository->getMembers($team);

        return new Response($twig->render('@Parthenon/user/athena/team/members.html.twig', ['members' => $members]));
    }

    public function getMenuOptions(): array
    {
        return [];
    }
}
