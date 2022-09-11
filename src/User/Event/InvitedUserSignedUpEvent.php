<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class InvitedUserSignedUpEvent extends Event
{
    public const NAME = 'parthenon.user.invite.signed_up';
    private UserInterface $user;
    private InviteCode $inviteCode;

    public function __construct(UserInterface $user, InviteCode $inviteCode)
    {
        $this->user = $user;
        $this->inviteCode = $inviteCode;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getInviteCode(): InviteCode
    {
        return $this->inviteCode;
    }
}
