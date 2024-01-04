<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
