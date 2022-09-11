<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

use Ramsey\Uuid\UuidInterface;

interface ExperimentLogRepositoryInterface
{
    public function saveDecision(UuidInterface $sessionId, string $experimentName, string $decisionOutput): void;

    public function deleteAllForSession(UuidInterface $sessionId): void;
}
