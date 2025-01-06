<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Billing\Plan;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Subscription\SubscriptionProviderInterface;

class CustomerPlanInfo implements CustomerPlanInfoInterface
{
    public function __construct(
        private SubscriptionProviderInterface $subscriptionProvider,
        private PlanManagerInterface $planManager,
    ) {
    }

    public function hasFeature(CustomerInterface $customer, string $featureCode): bool
    {
        $active = $this->subscriptionProvider->getSubscriptionsForCustomer($customer);

        foreach ($active as $subscription) {
            $plan = $this->planManager->getPlanByName($subscription->getPlanName());

            foreach ($plan->getFeatures() as $code) {
                if ($featureCode === $code) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getLimitCount(CustomerInterface $customer, string $limitCode): int
    {
        $active = $this->subscriptionProvider->getSubscriptionsForCustomer($customer);

        $count = 0;
        foreach ($active as $subscription) {
            $plan = $this->planManager->getPlanByName($subscription->getPlanName());

            foreach ($plan->getLimits() as $code => $limit) {
                if ($limitCode === $code) {
                    if (is_array($limit)) {
                        $count += $limit['limit'];
                    } else {
                        $count += $limit;
                    }
                }
            }
        }

        return $count;
    }
}
