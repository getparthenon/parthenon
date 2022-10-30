<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Controller;

use Parthenon\Athena\DashboardSectionManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
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
