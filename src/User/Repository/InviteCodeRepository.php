<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\User\Entity\InviteCode;

class InviteCodeRepository extends DoctrineRepository implements InviteCodeRepositoryInterface
{
    public function findActiveByCode(string $code): InviteCode
    {
        $inviteCode = $this->entityRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$inviteCode) {
            throw new NoEntityFoundException();
        }

        return $inviteCode;
    }
}
