<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Exception\CurrentUserNotATeamMemberException;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Symfony\Component\Security\Core\Security;

final class CurrentTeamProvider implements CurrentTeamProviderInterface
{
    public function __construct(private Security $security, private TeamRepositoryInterface $teamRepository)
    {
    }

    public function getCurrentTeam(): TeamInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof MemberInterface) {
            throw new CurrentUserNotATeamMemberException('Not a team member');
        }

        return $this->teamRepository->getByMember($user);
    }
}
