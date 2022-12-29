<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan\Counter;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Repository\ActiveMembersRepositoryInterface;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;

final class TeamInviteCounter implements TeamInviteCounterInterface
{
    public function __construct(
        private TeamInviteCodeRepositoryInterface $inviteCodeRepository,
        private ActiveMembersRepositoryInterface $userRepository,
        private TeamRepositoryInterface $teamRepository
    ) {
    }

    public function getCount(LimitedUserInterface $user): int
    {
        if (!$user instanceof MemberInterface) {
            throw new GeneralException('User does not implement MemberInterface');
        }

        $team = $this->teamRepository->getByMember($user);

        $inviteCount = $this->inviteCodeRepository->getUsableInviteCount($team);
        $activeMemberCount = $this->userRepository->getCountForActiveTeamMemebers($team);

        return $inviteCount + $activeMemberCount;
    }
}
