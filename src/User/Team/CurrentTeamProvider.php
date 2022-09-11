<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
