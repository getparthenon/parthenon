<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\UuidInterface;

interface SessionRepositoryInterface
{
    public function createSession(string $userAgent, string $ipAddress, ?UserInterface $user = null): UuidInterface;

    public function attachUserToSession(UuidInterface $sessionId, UserInterface $user): void;

    public function deleteSession(UuidInterface $sessionId): void;

    public function findAll(): \Generator;
}
