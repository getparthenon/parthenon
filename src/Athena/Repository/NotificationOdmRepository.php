<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Repository;

use Parthenon\Common\Exception\GeneralException;

class NotificationOdmRepository extends OdmCrudRepository implements NotificationRepositoryInterface
{
    public function getAllUnread(): array
    {
        try {
            return $this->documentRepository->findBy(['isRead' => false]);
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
