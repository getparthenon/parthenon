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

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;

class CustomerPlanInfo implements CustomerPlanInfoInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private PlanManagerInterface $planManager,
    ) {
    }

    public function hasFeature(CustomerInterface $customer, string $featureCode): bool
    {
        $active = $this->subscriptionRepository->getAllActiveForCustomer($customer);

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
        $active = $this->subscriptionRepository->getAllActiveForCustomer($customer);

        $count = 0;
        foreach ($active as $subscription) {
            $plan = $this->planManager->getPlanByName($subscription->getPlanName());

            foreach ($plan->getLimits() as $code => $limit) {
                if ($limitCode === $code) {
                    $count += $limit;
                }
            }
        }

        return $count;
    }
}
