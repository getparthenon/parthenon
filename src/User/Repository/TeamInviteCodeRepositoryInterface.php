<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
