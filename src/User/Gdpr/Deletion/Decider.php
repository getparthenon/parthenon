<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\UserInterface;

final class Decider implements VoterInterface
{
    /**
     * @var VoterInterface[]
     */
    private array $voters = [];

    public function add(VoterInterface $voter): self
    {
        $this->voters[] = $voter;

        return $this;
    }

    public function canDelete(UserInterface $user): bool
    {
        foreach ($this->voters as $voter) {
            if (!$voter->canDelete($user)) {
                return false;
            }
        }

        return true;
    }
}
