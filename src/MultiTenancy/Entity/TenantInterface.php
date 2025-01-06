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

namespace Parthenon\MultiTenancy\Entity;

interface TenantInterface
{
    public function getId();

    public function setId($id): void;

    public function getCreatedAt(): \DateTime;

    public function setCreatedAt(\DateTime $createdAt): void;

    public function getUpdatedAt(): ?\DateTime;

    public function setUpdatedAt(?\DateTime $updatedAt): void;

    public function getDeletedAt(): ?\DateTime;

    public function setDeletedAt(?\DateTime $deletedAt): void;

    public function getSubdomain(): string;

    public function setSubdomain(string $subdomain): void;

    public function getDatabase(): string;

    public function setDatabase(string $database): void;
}
