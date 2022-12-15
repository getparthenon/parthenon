<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PreInviteEvent extends Event
{
    public const NAME = 'parthenon.user.invite.pre';
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
