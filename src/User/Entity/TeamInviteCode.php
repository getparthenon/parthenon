<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
