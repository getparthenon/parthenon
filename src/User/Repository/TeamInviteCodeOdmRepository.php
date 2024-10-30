<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
