<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan;

use Parthenon\Payments\Exception\NoPlanFoundException;
use Parthenon\Payments\Repository\SubscriberRepositoryInterface;

final class PlanManager implements PlanManagerInterface
{
    /**
     * @var Plan[]
     */
    private array $plans = [];

    public function __construct(array $plans, private SubscriberRepositoryInterface $subscriberRepository)
    {
        foreach ($plans as $planName => $planInfo) {
            $plan = new Plan(
                $planName,
                $planInfo['limit'],
                $planInfo['features'] ?? [],
                $planInfo['yearly_price_id'] ?? '',
                $planInfo['monthly_price_id'] ?? '',
                $planInfo['is_free'] ?? false,
                $planInfo['is_per_seat'] ?? false,
                $planInfo['user_count'] ?? 1
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
        $subscription = $this->subscriberRepository->getSubscriptionForUser($limitedUser);
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
