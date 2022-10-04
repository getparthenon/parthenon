<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

class TeamRepository extends DoctrineCrudRepository implements TeamRepositoryInterface
{
    public function getByMember(MemberInterface $member): TeamInterface
    {
        $team = $member->getTeam();
        $this->entityRepository->getEntityManager()->refresh($team);

        return $team;
    }
}
