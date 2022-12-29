<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
