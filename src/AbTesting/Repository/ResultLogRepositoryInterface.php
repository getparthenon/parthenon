<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\UuidInterface;

interface ResultLogRepositoryInterface
{
    public function saveResult(UuidInterface $sessionId, string $resultId, ?UserInterface $user): void;

    public function deleteAllForSession(UuidInterface $sessionId): void;
}
