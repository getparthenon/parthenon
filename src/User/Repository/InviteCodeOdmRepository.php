<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\OdmRepository;
use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;

class InviteCodeOdmRepository extends OdmRepository implements InviteCodeRepositoryInterface
{
    public function findActiveByCode(string $code): InviteCode
    {
        $inviteCode = $this->documentRepository->findOneBy(['code' => $code, 'used' => false]);

        if (!$inviteCode instanceof InviteCode) {
            throw new NoEntityFoundException();
        }

        return $inviteCode;
    }

    public function hasInviteForEmailAndTeam(string $email, TeamInterface $team): bool
    {
        $inviteCode = $this->documentRepository->findOneBy(['email' => $email, 'team' => $team]);

        if (!$inviteCode) {
            return false;
        }

        return true;
    }
}
