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

use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DatabasePlanManagerTest extends TestCase
{
    public function testFetchFromDatabase()
    {
        $dummyFeature = new SubscriptionFeature();
        $dummyFeature->setName('Dummy Feature');
        $dummyFeature->setCode('dummy_feature');
        $dummyFeature->setDescription('A dummy feature for this test');

        $dummyLimit = new SubscriptionFeature();
        $dummyLimit->setCode('dummy_limit');
        $dummyLimit->setName('Dummy Limit');
        $dummyLimit->setDescription('A dummy limit');

        $dummyPlanLimit = new SubscriptionPlanLimit();
        $dummyPlanLimit->setSubscriptionFeature($dummyLimit);
        $dummyPlanLimit->setLimit(10);

        $dummyPrice = new Price();
        $dummyPrice->setAmount(1000);
        $dummyPrice->setCurrency('USD');
        $dummyPrice->setSchedule('week');
        $dummyPrice->setRecurring(true);

        $dummyPlan = new SubscriptionPlan();
        $dummyPlan->setName('Dummy Plan');
        $dummyPlan->setFree(false);
        $dummyPlan->setPerSeat(false);
        $dummyPlan->setPublic(false);
        $dummyPlan->setUserCount(10);
        $dummyPlan->setFeatures([$dummyFeature]);
        $dummyPlan->setLimits([$dummyPlanLimit]);
        $dummyPlan->setPrices([$dummyPrice]);

        $plans = [
            $dummyPlan,
        ];

        $repository = $this->createMock(SubscriptionPlanRepositoryInterface::class);
        $repository->method('getAll')->willReturn($plans);

        $subject = new DatabasePlanManager($repository);
        $actual = $subject->getPlans();

        $this->assertCount(1, $actual);

        /** @var Plan $actualPlan */
        $actualPlan = current($actual);
        $this->assertEquals('Dummy Plan', $actualPlan->getName());
        $this->assertEquals(10, $actualPlan->getUserCount());
        $this->assertFalse($actualPlan->isFree());
        $this->assertFalse($actualPlan->isPublic());
        $this->assertFalse($actualPlan->isPerSeat());

        $actualPrices = $actualPlan->getPublicPrices();
        $this->assertCount(1, $actualPrices);

        /** @var PlanPrice $actualPrice */
        $actualPrice = current($actualPrices);
        $this->assertEquals('10.00', $actualPrice->getAmount());
        $this->assertEquals('USD', $actualPrice->getCurrency());
    }
}
