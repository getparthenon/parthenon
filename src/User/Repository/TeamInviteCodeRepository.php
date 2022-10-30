<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamInviteCode;

class TeamInviteCodeRepository extends DoctrineRepository implements TeamInviteCodeRepositoryInterface
{
    /**
     * @return TeamInviteCode[]
     */
    public function findAllUnusedInvitesForTeam(TeamInterface $team): array
    {
        return $this->entityRepository->findBy(['team' => $team, 'used' => false]);
    }

    public function getUsableInviteCount(TeamInterface $team): int
    {
        return $this->entityRepository->count(['team' => $team, 'used' => false, 'cancelled' => false]);
    }

    public function hasInviteForEmailAndTeam(string $email, TeamInterface $team): bool
    {
        $inviteCode = $this->entityRepository->findOneBy(['email' => $email, 'team' => $team]);

        if (!$inviteCode) {
            return false;
        }

        return true;
    }

    public function findActiveByCode(string $code): TeamInviteCode
    {
        $inviteCode = $this->entityRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$inviteCode instanceof TeamInviteCode) {
            throw new NoEntityFoundException();
        }

        return $inviteCode;
    }
}
