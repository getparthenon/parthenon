<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\UserInterface;

interface VoterInterface
{
    public function canDelete(UserInterface $user): bool;
}
