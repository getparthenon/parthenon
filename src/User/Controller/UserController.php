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

namespace Parthenon\User\Controller;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Exception\PasswordResetInvalidException;
use Parthenon\User\Formatter\UserFormatterInterface;
use Parthenon\User\RequestProcessor\ChangePassword;
use Parthenon\User\RequestProcessor\ConfirmEmail;
use Parthenon\User\RequestProcessor\InviteUser;
use Parthenon\User\RequestProcessor\PasswordReset;
use Parthenon\User\RequestProcessor\PasswordResetConfirm;
use Parthenon\User\RequestProcessor\Settings;
use Parthenon\User\RequestProcessor\UserSignup;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController
{
    #[Route('/user/signup/{code}', name: 'parthenon_user_signup_invited')]
    #[Route('/user/signup', name: 'parthenon_user_signup')]
    #[Template('user/signup.html.twig')]
    public function signup(Request $request, UserSignup $processor, LoggerInterface $logger)
    {
        $logger->info('User sign up page called');

        return $processor->process($request);
    }

    #[Route('/user/signedup', name: 'parthenon_user_signed_up')]
    #[Template('user/signedup.html.twig')]
    public function signedup(Request $request, LoggerInterface $logger)
    {
        $logger->info('User signed up page called');

        return [];
    }

    #[Route('/user/reset', name: 'parthenon_user_reset')]
    #[Template('user/reset.html.twig')]
    public function reset(Request $request, PasswordReset $processor, LoggerInterface $logger)
    {
        $logger->info('User reset password page called');

        return $processor->process($request);
    }

    #[Route('/user/password', name: 'parthenon_user_change_password')]
    #[Template('user/change_password.html.twig')]
    public function changePassword(Request $request, ChangePassword $processor, LoggerInterface $logger)
    {
        $logger->info('User change password page called');

        return $processor->process($request);
    }

    #[Route('/user/invite', name: 'parthenon_user_invite')]
    #[Template('user/invite.html.twig')]
    public function inviteUser(Request $request, InviteUser $processor, LoggerInterface $logger)
    {
        $logger->info('User invite page called');

        return $processor->process($request);
    }

    #[Route('/user/reset/{code}', name: 'parthenon_user_reset_confirm')]
    #[Template('user/reset_confirm.html.twig')]
    public function resetConfirm(Request $request, PasswordResetConfirm $processor, LoggerInterface $logger)
    {
        $logger->info('User confirm reset password page called');

        try {
            return $processor->process($request);
        } catch (NoEntityFoundException|PasswordResetInvalidException $e) {
            $logger->warning('An error occured during a user reset');
            throw new NotFoundHttpException();
        }
    }

    #[Route('/user/settings', name: 'parthenon_user_settings')]
    #[Template('user/settings.html.twig')]
    public function profile(Request $request, Settings $processor, LoggerInterface $logger)
    {
        $logger->info('User settings page called');

        return $processor->process($request);
    }

    #[Route('/user/confirm/{confirmationCode}', name: 'parthenon_user_confirm')]
    public function confirmEmail(Request $request, ConfirmEmail $processor, LoggerInterface $logger)
    {
        $logger->info('User confirm email page called');

        return $processor->process($request);
    }

    #[Route('/login', name: 'parthenon_user_login')]
    #[Template('user/login.html.twig')]
    public function login(LoggerInterface $logger, AuthenticationUtils $authenticationUtils, Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $logger->info('User login page called');
        $user = $security->getUser();
        if ($user) {
            return new RedirectResponse($urlGenerator->generate('parthenon_main_index'));
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error,
        ];
    }

    #[Route('/authenticate', name: 'parthenon_user_authenticate')]
    public function authenticate(LoggerInterface $logger, AuthenticationUtils $authenticationUtils, Security $security, UserFormatterInterface $userFormatter): JsonResponse
    {
        $logger->info('User login page called');
        $user = $security->getUser();
        if ($user) {
            return new JsonResponse($userFormatter->format($user));
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return new JsonResponse([
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'parthenon_user_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
