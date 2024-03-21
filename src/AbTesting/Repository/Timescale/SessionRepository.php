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
use Parthenon\AbTesting\Repository\SessionRepositoryInterface;
use Parthenon\User\Entity\UserInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class SessionRepository implements SessionRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function createSession(string $userAgent, string $ipAddress, ?UserInterface $user = null): UuidInterface
    {
        $uuid = Uuid::uuid4();
        $userId = null;
        if ($user) {
            $userId = (string) $user->getId();
        }
        $now = new \DateTime('now');
        $query = $this->connection->prepare('INSERT INTO ab_sessions (id, user_id, user_agent, ip_address, created_at) VALUES (:id, :user_id, :user_agent, :ip_address, :created_at)');
        $query->bindValue(':id', (string) $uuid);
        $query->bindValue(':user_id', $userId);
        $query->bindValue(':user_agent', $userAgent);
        $query->bindValue(':ip_address', $ipAddress);
        $query->bindValue(':created_at', $now->format('Y-m-d H:i:s'));
        $query->executeQuery();

        return $uuid;
    }

    public function attachUserToSession(UuidInterface $sessionId, UserInterface $user): void
    {
        $query = $this->connection->prepare('UPDATE ab_sessions SET user_id = :user_id WHERE id = :id');
        $query->bindValue(':id', (string) $sessionId);
        $query->bindValue(':user_id', (string) $user->getId());
        $query->executeQuery();
    }

    public function deleteSession(UuidInterface $sessionId): void
    {
        $statement = $this->connection->prepare('DELETE FROM ab_sessions WHERE id = :session_id');
        $statement->bindValue(':session_id', (string) $sessionId);
        $statement->executeQuery();
    }

    public function findAll(): \Generator
    {
        $results = $this->connection->executeQuery('SELECT * FROM ab_sessions');

        while ($row = $results->fetchAssociative()) {
            yield $row;
        }
    }
}
