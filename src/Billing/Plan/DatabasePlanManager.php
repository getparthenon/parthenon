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
            $subscriptionPlan->getHasTrial(),
            $subscriptionPlan->getTrialLengthDays(),
            $subscriptionPlan->getId()
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
                'entity_id' => $price->getId(),
            ];
        }

        return $output;
    }
}
