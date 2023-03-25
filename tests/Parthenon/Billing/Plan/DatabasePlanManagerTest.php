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

use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DatabasePlanManagerTest extends TestCase
{
    public function testFetchFromDatabase()
    {
        $dummyPlan = new SubscriptionPlan();
        $dummyPlan->setName('Dummy Plan');
        $dummyPlan->setFree(false);
        $dummyPlan->setPerSeat(false);
        $dummyPlan->setPublic(false);
        $dummyPlan->setUserCount(10);
        $dummyPlan->setFeatures([]);
        $dummyPlan->setLimits([]);
        $dummyPlan->setPrices([]);

        $plans = [
              $dummyPlan,
        ];

        $repository = $this->createMock(SubscriptionPlanRepositoryInterface::class);
        $repository->method('getAll')->willReturn($plans);

        $subject = new DatabasePlanManager($repository);
        $actual = $subject->getPlans();

        $this->assertCount(1, $actual);
    }
}
