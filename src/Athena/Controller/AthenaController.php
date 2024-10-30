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

namespace Parthenon\Athena\Controller;

use Parthenon\Athena\DashboardSectionManager;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AthenaController
{
    #[Template('@Parthenon/athena/index.html.twig')]
    public function index(LoggerInterface $logger, DashboardSectionManager $dashboardSectionManager)
    {
        $logger->info('The Athena welcome page called');

        return ['sections' => $dashboardSectionManager->getDashboardSections()];
    }

    #[Template('@Parthenon/athena/login/login.html.twig')]
    public function login(LoggerInterface $logger, AuthenticationUtils $authenticationUtils, Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $logger->info('The Athena User login page called');
        $user = $security->getUser();
        if ($user) {
            return new RedirectResponse($urlGenerator->generate('parthenon_athena_index'));
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error?->getMessage(),
        ];
    }
}
