<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
