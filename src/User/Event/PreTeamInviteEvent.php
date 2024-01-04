<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;

final class PreTeamInviteEvent
{
    public const NAME = 'parthenon.user.team_invite.pre';
    private UserInterface $user;
    private TeamInterface $team;
    private InviteCode $inviteCode;

    public function __construct(UserInterface $user, TeamInterface $team, InviteCode $inviteCode)
    {
        $this->user = $user;
        $this->team = $team;
        $this->inviteCode = $inviteCode;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getTeam(): TeamInterface
    {
        return $this->team;
    }

    public function setTeam(TeamInterface $team): void
    {
        $this->team = $team;
    }

    public function getInviteCode(): InviteCode
    {
        return $this->inviteCode;
    }

    public function setInviteCode(InviteCode $inviteCode): void
    {
        $this->inviteCode = $inviteCode;
    }
}
