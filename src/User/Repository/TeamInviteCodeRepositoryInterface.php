<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamInviteCode;

interface TeamInviteCodeRepositoryInterface extends RepositoryInterface
{
    /**
     * @return TeamInviteCode[]
     */
    public function findAllUnusedInvitesForTeam(TeamInterface $team): array;

    public function getUsableInviteCount(TeamInterface $team): int;

    public function hasInviteForEmailAndTeam(string $email, TeamInterface $team): bool;

    /**
     * @throws NoEntityFoundException
     */
    public function findActiveByCode(string $code): TeamInviteCode;
}
