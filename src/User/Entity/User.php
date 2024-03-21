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

use Doctrine\Common\Collections\Collection;
use Parthenon\Athena\Entity\DeletableInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class User implements UserInterface, EquatableInterface, DeletableInterface
{
    public const DEFAULT_ROLE = 'ROLE_USER';
    protected $id;

    #[Assert\NotBlank(message: 'parthenon.user.validation.email.not_blank')]
    #[Assert\Email(message: 'parthenon.user.validation.email.email')]
    protected string $email;

    #[Assert\NotBlank(message: 'parthenon.user.validation.password.not_blank')]
    #[Assert\Length(min: 8, minMessage: 'parthenon.user.validation.password.length')]
    protected string $password;
    protected ?string $name = null;
    protected string $confirmationCode;
    protected \DateTime $createdAt;
    protected ?\DateTime $activatedAt;
    protected ?\DateTime $deactivatedAt;
    protected ?\DateTimeInterface $deletedAt;
    protected bool $isConfirmed = false;
    protected $isDeleted = false;
    protected $roles = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        if ($this->roles instanceof Collection) {
            return $this->roles->toArray();
        }

        return $this->roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials(): void
    {
        $this->password = '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(string $confirmationCode): void
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getActivatedAt(): ?\DateTime
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTime $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }

    public function getDeactivatedAt(): ?\DateTime
    {
        return $this->deactivatedAt;
    }

    public function setDeactivatedAt(?\DateTime $deactivatedAt): void
    {
        $this->deactivatedAt = $deactivatedAt;
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function markAsDeleted(): self
    {
        $this->isDeleted = true;
        $this->deletedAt = new \DateTime('Now');

        return $this;
    }

    public function unmarkAsDeleted(): self
    {
        $this->isDeleted = false;
        $this->deletedAt = null;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
