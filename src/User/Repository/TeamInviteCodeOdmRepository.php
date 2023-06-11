<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\OdmRepository;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamInviteCode;

class TeamInviteCodeOdmRepository extends OdmRepository implements TeamInviteCodeRepositoryInterface
{
    public function hasInviteForEmailAndTeam(string $email, TeamInterface $team): bool
    {
        $inviteCode = $this->documentRepository->findOneBy(['email' => $email, 'team' => $team]);

        if (!$inviteCode) {
            return false;
        }

        return true;
    }

    public function findAllUnusedInvitesForTeam(TeamInterface $team): array
    {
        return $this->documentRepository->createQueryBuilder()
            ->field('team')->equals($team)
            ->field('used')->equals(false)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function getUsableInviteCount(TeamInterface $team): int
    {
        return $this->documentRepository->createQueryBuilder()->count()
            ->field('team')->equals($team)
            ->field('used')->equals(false)
            ->field('cancelled')->equals(false)
            ->getQuery()
            ->execute();
    }

    public function findActiveByCode(string $code): TeamInviteCode
    {
        $inviteCode = $this->documentRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$inviteCode instanceof TeamInviteCode) {
            throw new NoEntityFoundException();
        }

        return $inviteCode;
    }
}
