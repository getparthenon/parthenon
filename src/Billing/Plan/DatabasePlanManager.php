<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Plan;

use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;

class DatabasePlanManager implements PlanManagerInterface
{
    public function __construct(private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository)
    {
    }

    public function getPlans(): array
    {
        $planEntities = $this->subscriptionPlanRepository->getAll();

        $output = [];
        foreach ($planEntities as $planEntity) {
            $output[] = $this->convertPlan($planEntity);
        }

        return $output;
    }

    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan
    {
        // TODO: Implement getPlanForUser() method.
    }

    public function getPlanByName(string $planName): Plan
    {
        // TODO: Implement getPlanByName() method.
    }

    protected function convertPlan(SubscriptionPlan $subscriptionPlan): Plan
    {
        $plan = new Plan(
            $subscriptionPlan->getName(),
            $this->convertLimits($subscriptionPlan->getLimits()),
            $this->convertFeatures(),
            $this->convertPrices(),
            $subscriptionPlan->isFree(),
            $subscriptionPlan->isPerSeat(),
            $subscriptionPlan->getUserCount(),
            $subscriptionPlan->isPublic(),
        );

        return $plan;
    }

    /**
     * @param SubscriptionPlanLimit[] $limits
     */
    protected function convertLimits(array|Collection $limits): array
    {
        $output = [];

        foreach ($limits as $limit) {
            $output[$limit->getSubscriptionLimit()->getCode()] = [
                'name' => $limit->getSubscriptionLimit()->getName(),
                'code' => $limit->getSubscriptionLimit()->getCode(),
                'limit' => $limit->getLimit(),
                'description' => $limit->getSubscriptionLimit()->getDescription(),
            ];
        }

        return $output;
    }

    protected function convertFeatures(): array
    {
        $output = [];

        return $output;
    }

    protected function convertPrices(): array
    {
        $output = [];

        return $output;
    }
}
