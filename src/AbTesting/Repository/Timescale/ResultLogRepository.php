<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
