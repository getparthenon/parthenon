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

use DateTime;

class ForgotPasswordCode
{
    protected $id;
    protected string $code;
    protected UserInterface $user;
    protected bool $used;
    protected DateTime $createdAt;
    protected DateTime $expiresAt;
    protected ?DateTime $usedAt;

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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

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

    public function isExpired()
    {
        $now = new \DateTime('now');

        if ($this->expiresAt < $now) {
            return true;
        }

        return false;
    }
}
