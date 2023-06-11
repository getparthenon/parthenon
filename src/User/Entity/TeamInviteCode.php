<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Entity;

class TeamInviteCode extends InviteCode
{
    protected ?TeamInterface $team;

    public function getTeam(): ?TeamInterface
    {
        return $this->team;
    }

    public function setTeam(?TeamInterface $team): void
    {
        $this->team = $team;
    }

    public static function createForUserAndTeam(UserInterface $user, TeamInterface $team, string $email, string $role): self
    {
        $self = static::createForUser($user, $email, $role);
        $self->setTeam($team); /* @phpstan-ignore-line */

        return $self; /* @phpstan-ignore-line */
    }

    public function hasRole(): bool
    {
        return isset($this->role) && !empty($this->role);
    }
}
