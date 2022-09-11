<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class TeamInvitedUserSignedUpEvent extends Event
{
    public const NAME = 'parthenon.user.team.invite.signed_up';
    private UserInterface $user;
    private InviteCode $inviteCode;
    private TeamInterface $team;

    public function __construct(UserInterface $user, InviteCode $inviteCode, TeamInterface $team)
    {
        $this->user = $user;
        $this->inviteCode = $inviteCode;
        $this->team = $team;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getInviteCode(): InviteCode
    {
        return $this->inviteCode;
    }

    public function getTeam(): TeamInterface
    {
        return $this->team;
    }
}
