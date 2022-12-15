<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Security\Voter;

use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamOwnedInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TeamVoter extends Voter
{
    private TeamRepositoryInterface $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!$subject instanceof TeamOwnedInterface) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof MemberInterface) {
            return false;
        }

        if (!$subject instanceof TeamOwnedInterface) {
            return true;
        }

        // workaround for Doctrine. Get latest and not the one within the document
        $team = $this->teamRepository->findById($subject->getOwningTeam()->getId());

        return $team->hasMember($user);
    }
}
