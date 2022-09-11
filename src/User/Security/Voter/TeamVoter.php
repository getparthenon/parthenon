<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
