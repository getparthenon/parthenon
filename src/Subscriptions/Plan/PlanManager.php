<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

use Parthenon\Subscriptions\Exception\NoPlanFoundException;
use Parthenon\Subscriptions\Repository\SubscriberRepositoryInterface;

final class PlanManager implements PlanManagerInterface
{
    /**
     * @var Plan[]
     */
    private array $plans = [];

    public function __construct(array $plans, private SubscriberRepositoryInterface $subscriberRepository)
    {
        foreach ($plans as $planName => $planInfo) {
            $plan = new Plan($planName, $planInfo['limit'], $planInfo['features'] ?? [], $planInfo['yearly_price_id'] ?? '', $planInfo['monthly_price_id'] ?? '', $planInfo['is_free'] ?? false);
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
