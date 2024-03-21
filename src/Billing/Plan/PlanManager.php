<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Billing\Plan;

use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;

final class PlanManager implements PlanManagerInterface
{
    /**
     * @var Plan[]
     */
    private array $plans = [];

    public function __construct(array $rawPlans, private CustomerRepositoryInterface $customerRepository)
    {
        foreach ($rawPlans as $planName => $planInfo) {
            $plan = new Plan(
                $planName,
                $planInfo['limit'],
                $planInfo['features'] ?? [],
                $planInfo['prices'] ?? [],
                $planInfo['is_free'] ?? false,
                $planInfo['is_per_seat'] ?? false,
                $planInfo['user_count'] ?? 1,
                $planInfo['public'] ?? true,
                $planInfo['has_trial'] ?? false,
                $planName['trial_length_days'] ?? 0,
            );
            $this->plans[] = $plan;
        }
    }

    /**
     * @return Plan[]
     */
    public function getPlans(): array
    {
        return $this->plans;
    }

    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan
    {
        $subscription = $this->customerRepository->getSubscriptionForUser($limitedUser);

        foreach ($this->plans as $plan) {
            if ($plan->getName() === $subscription->getPlanName()) {
                return $plan;
            }
        }

        throw new NoPlanFoundException(sprintf("No plan '%s' found for user", $limitedUser->getPlanName()));
    }

    public function getPlanByName(string $planName): Plan
    {
        foreach ($this->plans as $plan) {
            if ($plan->getName() === $planName) {
                return $plan;
            }
        }

        throw new NoPlanFoundException(sprintf("No plan '%s' found for user", $planName));
    }
}
