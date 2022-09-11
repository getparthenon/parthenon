<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;

final class PostTeamInviteEvent
{
    public const NAME = 'parthenon.user.team_invite.post';
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
