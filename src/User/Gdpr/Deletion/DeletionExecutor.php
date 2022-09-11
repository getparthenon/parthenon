<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Entity\UserInterface;

final class DeletionExecutor
{
    use LoggerAwareTrait;

    private VoterInterface $voter;
    private DeleterInterface $deleter;

    public function __construct(VoterInterface $voter, DeleterInterface $deleter)
    {
        $this->voter = $voter;
        $this->deleter = $deleter;
    }

    public function delete(UserInterface $user): void
    {
        if (!$this->voter->canDelete($user)) {
            $this->getLogger()->info("User can't be deleted.", ['user_id' => (string) $user->getId()]);

            return;
        }
        $this->getLogger()->info('Deleting user', ['user_id' => (string) $user->getId()]);

        $this->deleter->delete($user);
    }
}
