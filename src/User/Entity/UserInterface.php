<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Entity;

use Parthenon\Athena\Entity\DeletableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, PasswordAuthenticatedUserInterface, DeletableInterface
{
    public function setPassword(string $password);

    public function getPassword(): ?string;

    public function setEmail(string $email);

    public function getEmail();

    public function getName(): ?string;

    public function setName(string $name);

    public function setConfirmationCode(string $confirmationCode);

    public function getConfirmationCode();

    public function isConfirmed(): bool;

    public function setIsConfirmed(bool $isConfirmed): void;

    public function setCreatedAt(\DateTime $dateTime);

    public function setActivatedAt(?\DateTime $dateTime);

    public function getId();

    public function setRoles(array $roles);
}
