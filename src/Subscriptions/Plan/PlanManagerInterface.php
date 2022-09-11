<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

use Parthenon\Subscriptions\Exception\NoPlanFoundException;

interface PlanManagerInterface
{
    /**
     * @return Plan[]
     */
    public function getPlans(): array;

    /**
     * @throws NoPlanFoundException
     */
    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan;

    /**
     * @throws NoPlanFoundException
     */
    public function getPlanByName(string $planName): Plan;
}
