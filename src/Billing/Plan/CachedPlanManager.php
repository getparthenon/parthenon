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
use Parthenon\Common\LoggerAwareTrait;

class CachedPlanManager implements PlanManagerInterface
{
    use LoggerAwareTrait;

    public const REDIS_STORAGE_KEY = 'parthenon_plan';
    private ?array $plans = null;

    public function __construct(
        private PlanManagerInterface $planManager,
        private \Redis $redis,
    ) {
    }

    public function getPlans(): array
    {
        if (!isset($this->plans)) {
            $rawData = $this->redis->get(self::REDIS_STORAGE_KEY);

            if (!$rawData) {
                $this->getLogger()->debug('Fetching plans from original plan manager');
                $this->plans = $this->planManager->getPlans();
                $rawData = serialize($this->plans);
                $this->redis->set(self::REDIS_STORAGE_KEY, $rawData);
            } else {
                $this->getLogger()->debug('Got the plans from cache');
                $this->plans = unserialize($rawData);
            }
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
}
