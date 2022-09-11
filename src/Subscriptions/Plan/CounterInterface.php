<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

interface CounterInterface
{
    public function supports(LimitableInterface $limitable): bool;

    public function getCount(LimitedUserInterface $user, LimitableInterface $limitable): int;
}
