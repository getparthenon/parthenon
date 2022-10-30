<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Repository\Timescale;

use Doctrine\DBAL\Connection;
use Parthenon\AbTesting\Repository\ExperimentLogRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ExperimentLogRepository implements ExperimentLogRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function saveDecision(UuidInterface $sessionId, string $experimentName, string $decisionOutput): void
    {
        $uuid = Uuid::uuid4();
        $now = new \DateTime('now');
        $query = $this->connection->prepare('INSERT INTO ab_experiment_log (id, session_id, decision_string_id, decision_output, created_at) VALUES (:id, :session_id, :decision_string_id, :decision_output, :created_at)');
        $query->bindValue(':id', (string) $uuid);
        $query->bindValue(':session_id', (string) $sessionId);
        $query->bindValue(':decision_string_id', (string) $experimentName);
        $query->bindValue(':decision_output', (string) $decisionOutput);
        $query->bindValue(':created_at', $now->format('Y-m-d H:i:s'));
        $query->executeQuery();
    }

    public function deleteAllForSession(UuidInterface $sessionId): void
    {
        $statement = $this->connection->prepare('DELETE FROM ab_experiment_log WHERE session_id = :session_id');
        $statement->bindValue(':session_id', (string) $sessionId);
        $statement->executeQuery();
    }
}
