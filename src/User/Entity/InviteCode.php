<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Entity;

use DateTime;

class InviteCode
{
    public const AUTH_CHECKER_ATTRIBUTE = 'enable';

    protected $id;
    protected UserInterface $user;
    protected ?UserInterface $invitedUser;
    protected string $code;
    protected string $email;
    protected bool $used;
    protected DateTime $createdAt;
    protected ?DateTime $usedAt;
    protected bool $cancelled = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsedAt(): ?DateTime
    {
        return $this->usedAt;
    }

    public function setUsedAt(?DateTime $usedAt): self
    {
        $this->usedAt = $usedAt;

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getInvitedUser(): ?UserInterface
    {
        return $this->invitedUser;
    }

    public function setInvitedUser(?UserInterface $invitedUser): self
    {
        $this->invitedUser = $invitedUser;

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }

    public static function createForUser(UserInterface $user, string $email): self
    {
        $self = new static();
        $self->setUser($user)
            ->setEmail($email)
            ->setCode(bin2hex(random_bytes(32)))
            ->setUsed(false)
            ->setCreatedAt(new \DateTime('now'));

        return $self;
    }
}
