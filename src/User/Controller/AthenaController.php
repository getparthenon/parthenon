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
