<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan\Counter;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Subscriptions\Plan\CounterInterface;
use Parthenon\Subscriptions\Plan\LimitableInterface;
use Parthenon\Subscriptions\Plan\LimitedUserInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Repository\ActiveMembersRepositoryInterface;
use Parthenon\User\Repository\TeamInviteCodeRepositoryInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;

class TeamInviteCounter implements CounterInterface
{
    public function __construct(
        private TeamInviteCodeRepositoryInterface $inviteCodeRepository,
        private ActiveMembersRepositoryInterface $userRepository,
        private TeamRepositoryInterface $teamRepository
    ) {
    }

    public function supports(LimitableInterface $limitable): bool
    {
        return $limitable instanceof TeamInviteCode;
    }

    public function getCount(LimitedUserInterface $user, LimitableInterface $limitable): int
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
