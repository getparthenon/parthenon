<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Billing\BillaBear\Plan;

use BillaBear\Model\Feature;
use BillaBear\Model\Limit;
use BillaBear\Model\Price;
use BillaBear\Model\SubscriptionPlan;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;

final class BillaBearPlanManager implements PlanManagerInterface
{
    private ?array $plans = null;

    public function __construct(
        private SdkFactory $sdkFactory,
    ) {
    }

    public function getPlans(): array
    {
        if (!isset($this->plans)) {
            $data = $this->sdkFactory->createSubscriptionsApi()->listSubscriptionPlans(limit: 100);

            $output = [];
            foreach ($data->getData() as $planEntity) {
                $output[] = $this->convertPlan($planEntity);
            }
            $this->plans = $output;
        }

        return $output;
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
        return new Plan(
            $subscriptionPlan->getName(),
            $this->convertLimits($subscriptionPlan->getLimits()),
            $this->convertFeatures($subscriptionPlan->getFeatures()),
            $this->convertPrices($subscriptionPlan->getPrices()),
            $subscriptionPlan->getFree(),
            $subscriptionPlan->getPerSeat(),
            $subscriptionPlan->getUserCount(),
            $subscriptionPlan->getPublic(),
            $subscriptionPlan->getHasTrial(),
            $subscriptionPlan->getTrialLengthDays(),
            $subscriptionPlan->getId()
        );
    }

    /**
     * @param Limit[] $limits
     */
    protected function convertLimits(array $limits): array
    {
        $output = [];

        foreach ($limits as $limit) {
            $output[$limit->getFeature()->getCode()] = [
                'name' => $limit->getFeature()->getName(),
                'code' => $limit->getFeature()->getCode(),
                'limit' => $limit->getLimit(),
                'description' => $limit->getFeature()->getDescription(),
            ];
        }

        return $output;
    }

    /**
     * @param Feature[]|Collection $features
     */
    protected function convertFeatures(array $features): array
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
    protected function convertPrices(array $prices): array
    {
        $output = [];

        foreach ($prices as $price) {
            $schedule = $price->getSchedule();

            if (!isset($output[$schedule])) {
                $output[$schedule] = [];
            }

            $output[$schedule][$price->getCurrency()] = [
                'amount' => (string) $price->getAmount(),
                'currency' => $price->getCurrency(),
                'price_id' => $price->getExternalReference(),
                'public' => $price->getPublic(),
                'entity_id' => $price->getId(),
            ];
        }

        return $output;
    }
}
