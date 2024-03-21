<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
