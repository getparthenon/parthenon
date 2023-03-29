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
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;

class DatabasePlanManager implements PlanManagerInterface
{
    private ?array $plans = null;

    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
    ) {
    }

    /**
     * @return array|Plan[]
     */
    public function getPlans(): array
    {
        if (!isset($this->plans)) {
            $planEntities = $this->subscriptionPlanRepository->getAll();

            $output = [];
            foreach ($planEntities as $planEntity) {
                $output[] = $this->convertPlan($planEntity);
            }
            $this->plans = $output;
        }

        return $this->plans;
    }

    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan
    {
        return $this->getPlanByName($limitedUser->getPlanName());
    }

    public function getPlanByName(string $planName): Plan
    {
        $plans = $this->getPlans();

        foreach ($plans as $plan) {
            if ($plan->getName() === $planName) {
                return $plan;
            }
        }
        throw new NoPlanFoundException();
    }

    protected function convertPlan(SubscriptionPlan $subscriptionPlan): Plan
    {
        $plan = new Plan(
            $subscriptionPlan->getName(),
            $this->convertLimits($subscriptionPlan->getLimits()),
            $this->convertFeatures($subscriptionPlan->getFeatures()),
            $this->convertPrices($subscriptionPlan->getPrices()),
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
            $output[$limit->getSubscriptionFeature()->getCode()] = [
                'name' => $limit->getSubscriptionFeature()->getName(),
                'code' => $limit->getSubscriptionFeature()->getCode(),
                'limit' => $limit->getLimit(),
                'description' => $limit->getSubscriptionFeature()->getDescription(),
            ];
        }

        return $output;
    }

    /**
     * @param SubscriptionFeature[]|Collection $features
     */
    protected function convertFeatures(array|Collection $features): array
    {
        $output = [];

        foreach ($features as $feature) {
            $output[$feature->getCode()] = [
                'code' => $feature->getCode(),
                'name' => $feature->getName(),
                'description' => $feature->getDescription(),
            ];
        }

        return $output;
    }

    /**
     * @param Price[]|Collection $prices
     */
    protected function convertPrices(array|Collection $prices): array
    {
        $output = [];

        foreach ($prices as $price) {
            $schedule = $price->getSchedule();

            if (!isset($output[$schedule])) {
                $output[$schedule] = [];
            }

            $output[$schedule][$price->getCurrency()] = [
                'amount' => (string) $price->getAsMoney()->getAmount(),
                'currency' => $price->getCurrency(),
                'price_id' => $price->getExternalReference(),
                'public' => $price->isPublic(),
            ];
        }

        return $output;
    }
}
