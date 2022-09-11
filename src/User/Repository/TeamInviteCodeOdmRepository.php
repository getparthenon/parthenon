<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
