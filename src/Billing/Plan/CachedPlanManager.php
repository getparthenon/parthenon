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

class CachedPlanManager implements PlanManagerInterface
{
    private ?array $plans = null;

    public function __construct(
        private PlanManagerInterface $planManager,
        private \Redis $redis,
    ) {
    }

    public function getPlans(): array
    {
        if (!isset($this->plans)) {
            $rawData = $this->redis->get('parthenon_plans');

            if (!$rawData) {
                $this->plans = $this->planManager->getPlans();
                $rawData = serialize($this->plans);
                $this->redis->set('parthenon_plans', $rawData);
            } else {
                $this->plans = unserialize($rawData);
            }
        }

        return $this->plans;
    }

    public function getPlanForUser(LimitedUserInterface $limitedUser): Plan
    {
        // TODO: Implement getPlanForUser() method.
    }

    public function getPlanByName(string $planName): Plan
    {
        // TODO: Implement getPlanByName() method.
    }
}
