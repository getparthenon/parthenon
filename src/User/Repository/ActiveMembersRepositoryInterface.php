<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Repository;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

interface ActiveMembersRepositoryInterface
{
    /**
     * @return MemberInterface[]
     */
    public function getMembers(TeamInterface $team): array;

    public function getCountForActiveTeamMemebers(TeamInterface $team): int;
}
