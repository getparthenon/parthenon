<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
