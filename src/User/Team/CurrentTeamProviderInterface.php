<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Team;

use Parthenon\User\Entity\TeamInterface;

interface CurrentTeamProviderInterface
{
    public function getCurrentTeam(): TeamInterface;
}
