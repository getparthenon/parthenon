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

namespace Parthenon\User\Entity;

class ForgotPasswordCode
{
    protected $id;
    protected string $code;
    protected UserInterface $user;
    protected bool $used;
    protected \DateTime $createdAt;
    protected \DateTime $expiresAt;
    protected ?\DateTime $usedAt;

    public function __construct()
    {
    }

    public static function createForUser(UserInterface $user): self
    {
        $self = new static();
        $self->setUser($user)
             ->setCode(bin2hex(random_bytes(32)))
             ->setUsed(false)
             ->setCreatedAt(new \DateTime('now'))
             ->setExpiresAt(new \DateTime('+24 hours'));

        return $self;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getUserId()
    {
        return $this->user->getId();
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUsedAt(): ?\DateTime
    {
        return $this->usedAt;
    }

    public function setUsedAt(?\DateTime $usedAt): self
    {
        $this->usedAt = $usedAt;

        return $this;
    }

    public function isExpired()
    {
        $now = new \DateTime('now');

        if ($this->expiresAt < $now) {
            return true;
        }

        return false;
    }
}
