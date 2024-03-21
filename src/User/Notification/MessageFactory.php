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

namespace Parthenon\User\Notification;

use Parthenon\Common\Config;
use Parthenon\Notification\Email;
use Parthenon\User\Entity\ForgotPasswordCode;
use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;

class MessageFactory
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getUserSignUpMessage(UserInterface $user): Email
    {
        $message = UserEmail::createFromUser($user);
        $message->setSubject('User Signup')
            ->setContent(rtrim($this->config->getSiteUrl(), '/').'/confirm-email/'.$user->getConfirmationCode());

        return $message;
    }

    public function getPasswordResetMessage(UserInterface $user, ForgotPasswordCode $passwordReset): Email
    {
        $message = UserEmail::createFromUser($user);
        $message->setSubject('Reset Password')
            ->setContent(rtrim($this->config->getSiteUrl(), '/').'/forgot-password/'.$passwordReset->getCode());

        return $message;
    }

    public function getInviteMessage(UserInterface $user, InviteCode $inviteCode): Email
    {
        $message = UserEmail::createFromUser($user);
        $message->setSubject('Invited!')
            ->setContent(rtrim($this->config->getSiteUrl(), '/').'/signup/'.$inviteCode->getCode())
            ->setToName('Invited User')
            ->setToAddress($inviteCode->getEmail());

        return $message;
    }

    public function getTeamInviteMessage(UserInterface $user, TeamInterface $team, InviteCode $inviteCode): Email
    {
        $message = new Email();
        $message->setSubject('Invited!')
            ->setContent(rtrim($this->config->getSiteUrl(), '/').'/signup/'.$inviteCode->getCode())
            ->setToName('Invited User')
            ->setToAddress($inviteCode->getEmail());

        return $message;
    }
}
