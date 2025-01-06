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

use PHPUnit\Framework\TestCase;

class CachedPlanManagerTest extends TestCase
{
    public function testGetsDataFromRedis()
    {
        $decoratedPlanManager = $this->createMock(PlanManagerInterface::class);
        $redis = $this->createMock(\Redis::class);

        $plans = [
            new Plan(
                'a-test-plan',
                [],
                [],
                [],
                false,
                false,
                10
            ),
        ];
        $redis->method('get')->with(CachedPlanManager::REDIS_STORAGE_KEY)->willReturn(serialize($plans));

        $subject = new CachedPlanManager($decoratedPlanManager, $redis);
        $actual = $subject->getPlans();

        $this->assertEquals($plans, $actual);
    }

    public function testGetsDataFromDecoratedPlan()
    {
        $decoratedPlanManager = $this->createMock(PlanManagerInterface::class);
        $redis = $this->createMock(\Redis::class);

        $plans = [
            new Plan(
                'a-test-plan',
                [],
                [],
                [],
                false,
                false,
                10
            ),
        ];
        $redis->method('get')->with(CachedPlanManager::REDIS_STORAGE_KEY)->willReturn(false);
        $redis->expects($this->once())->method('set')->with(CachedPlanManager::REDIS_STORAGE_KEY, serialize($plans));

        $decoratedPlanManager->method('getPlans')->willReturn($plans);

        $subject = new CachedPlanManager($decoratedPlanManager, $redis);
        $actual = $subject->getPlans();

        $this->assertEquals($plans, $actual);
    }
}
