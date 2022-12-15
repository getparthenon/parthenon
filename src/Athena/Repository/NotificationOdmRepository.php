<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
