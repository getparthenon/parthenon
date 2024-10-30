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

namespace Parthenon\AbTesting\Repository\Timescale;

use Doctrine\DBAL\Connection;
use Parthenon\AbTesting\Repository\ResultLogRepositoryInterface;
use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ResultLogRepository implements ResultLogRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function saveResult(UuidInterface $sessionId, string $resultId, ?UserInterface $user): void
    {
        $id = Uuid::uuid4();
        $userId = null;
        $now = new \DateTime('now');

        if ($user instanceof UserInterface) {
            $userId = (string) $user->getId();
        }

        $statement = $this->connection->prepare('INSERT INTO ab_result_log (id, session_id, user_id, result_string_id, created_at) VALUES (:id, :session_id, :user_id, :result_string_id, :created_at)');
        $statement->bindValue(':id', (string) $id);
        $statement->bindValue(':session_id', (string) $sessionId);
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':result_string_id', $resultId);
        $statement->bindValue(':created_at', $now->format('Y-m-d H:i:s'));
        $statement->executeQuery();
    }

    public function deleteAllForSession(UuidInterface $sessionId): void
    {
        $statement = $this->connection->prepare('DELETE FROM ab_result_log WHERE session_id = :session_id');
        $statement->bindValue(':session_id', (string) $sessionId);
        $statement->executeQuery();
    }
}
