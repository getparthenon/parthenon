<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
        $query->execute();

        return $uuid;
    }

    public function attachUserToSession(UuidInterface $sessionId, UserInterface $user): void
    {
        $query = $this->connection->prepare('UPDATE ab_sessions SET user_id = :user_id WHERE id = :id');
        $query->bindValue(':id', (string) $sessionId);
        $query->bindValue(':user_id', (string) $user->getId());
        $query->execute();
    }

    public function deleteSession(UuidInterface $sessionId): void
    {
        $statement = $this->connection->prepare('DELETE FROM ab_sessions WHERE id = :session_id');
        $statement->bindValue(':session_id', (string) $sessionId);
        $statement->execute();
    }

    public function findAll(): \Generator
    {
        $results = $this->connection->executeQuery('SELECT * FROM ab_sessions');

        while ($row = $results->fetchAssociative()) {
            yield $row;
        }
    }
}
