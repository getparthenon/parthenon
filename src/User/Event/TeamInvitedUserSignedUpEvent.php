<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
