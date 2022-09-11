<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Repository;

class NotificationRepository extends DoctrineCrudRepository implements NotificationRepositoryInterface
{
    public function getAllUnread(): array
    {
        return $this->entityRepository->findBy(['isRead' => false]);
    }
}
