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
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Subscription\SubscriptionProviderInterface;
use PHPUnit\Framework\TestCase;

class CustomerPlanInfoTest extends TestCase
{
    public function testDoesNotHasFeature()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionProviderInterface::class);

        $subscriptionRepository->method('getSubscriptionsForCustomer')->with($customer)->willReturn([]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertFalse($subject->hasFeature($customer, 'feature'));
    }

    public function testDoesHasFeature()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionProviderInterface::class);
        $subscription = $this->createMock(Subscription::class);
        $plan = $this->createMock(Plan::class);

        $planManager->method('getPlanByName')->with('plan_name')->willReturn($plan);
        $plan->method('getFeatures')->willReturn(['feature']);
        $subscription->method('getPlanName')->willReturn('plan_name');
        $subscriptionRepository->method('getSubscriptionsForCustomer')->with($customer)->willReturn([$subscription]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertTrue($subject->hasFeature($customer, 'feature'));
    }

    public function testDoesNotHaveLimit()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionProviderInterface::class);

        $subscriptionRepository->method('getSubscriptionsForCustomer')->with($customer)->willReturn([]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertEquals(0, $subject->getLimitCount($customer, 'feature'));
    }

    public function testDoesHaveLimit()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionProviderInterface::class);
        $subscription = $this->createMock(Subscription::class);
        $subscriptionTwo = $this->createMock(Subscription::class);
        $plan = $this->createMock(Plan::class);
        $planTwo = $this->createMock(Plan::class);

        $planManager->expects($this->any())->method('getPlanByName')->will($this->returnValueMap(
            [
                ['plan_name', $plan],
                ['plan_name_two', $planTwo],
            ]
        ));

        $plan->method('getLimits')->willReturn(['feature' => 1]);
        $subscription->method('getPlanName')->willReturn('plan_name');

        $planTwo->method('getLimits')->willReturn(['feature' => 9]);
        $subscriptionTwo->method('getPlanName')->willReturn('plan_name_two');
        $subscriptionRepository->method('getSubscriptionsForCustomer')->with($customer)->willReturn([$subscription, $subscriptionTwo]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertEquals(10, $subject->getLimitCount($customer, 'feature'));
    }
}
