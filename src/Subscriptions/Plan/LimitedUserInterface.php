<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

use Parthenon\User\Entity\UserInterface;

interface LimitedUserInterface extends UserInterface
{
    public function getPlanName(): ?string;
}
