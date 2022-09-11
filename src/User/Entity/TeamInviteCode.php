<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Entity;

class TeamInviteCode extends InviteCode
{
    public const LIMITABLE_NAME = 'team_invite';

    protected ?TeamInterface $team;

    protected ?string $role;

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
        $self = static::createForUser($user, $email);
        $self->setRole($role); /* @phpstan-ignore-line */
        $self->setTeam($team); /* @phpstan-ignore-line */

        return $self; /* @phpstan-ignore-line */
    }

    public function getLimitableName(): string
    {
        return static::LIMITABLE_NAME;
    }

    public function hasRole(): bool
    {
        return isset($this->role) && !empty($this->role);
    }

    /**
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}
