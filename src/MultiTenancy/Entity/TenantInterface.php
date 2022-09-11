<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
