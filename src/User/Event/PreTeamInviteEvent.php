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
