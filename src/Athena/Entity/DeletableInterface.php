<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Entity;

interface DeletableInterface
{
    public function setDeletedAt(\DateTimeInterface $dateTime): self;

    public function isDeleted(): bool;

    public function markAsDeleted(): self;

    public function unmarkAsDeleted(): self;
}
