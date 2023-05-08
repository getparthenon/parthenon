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

    public function buildInviteCode(UserInterface $user, string $email, ?string $role = null): InviteCode
    {
        return InviteCode::createForUser($user, $email, $role);
    }

    public function buildTeamInviteCode(UserInterface $user, TeamInterface $team, string $email, string $role): TeamInviteCode
    {
        return TeamInviteCode::createForUserAndTeam($user, $team, $email, $role);
    }
}
