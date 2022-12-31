<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
    /**
     * @Assert\NotBlank(message="parthenon.user.validation.email.not_blank")
     * @Assert\Email(message="parthenon.user.validation.email.email")
     */
    protected string $email;
    /**
     * @Assert\NotBlank(message="parthenon.user.validation.password.not_blank")
     * @Assert\Length(min="8", minMessage="parthenon.user.validation.password.length")
     */
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

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
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

    /**
     * {@inheritdoc}
     */
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
