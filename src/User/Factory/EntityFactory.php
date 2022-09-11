<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Factory;

use Parthenon\User\Entity\ForgotPasswordCode;
use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Entity\UserInterface;

class EntityFactory
{
    public function buildPasswordReset(UserInterface $user): ForgotPasswordCode
    {
        return ForgotPasswordCode::createForUser($user);
    }

    public function buildInviteCode(UserInterface $user, string $email): InviteCode
    {
        return InviteCode::createForUser($user, $email);
    }

    public function buildTeamInviteCode(UserInterface $user, TeamInterface $team, string $email, string $role): TeamInviteCode
    {
        return TeamInviteCode::createForUserAndTeam($user, $team, $email, $role);
    }
}
