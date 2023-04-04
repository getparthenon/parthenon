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
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CustomerPlanInfoTest extends TestCase
{
    public function testDoesNotHasFeature()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $subscriptionRepository->method('getAllActiveForCustomer')->with($customer)->willReturn([]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertFalse($subject->hasFeature($customer, 'feature'));
    }

    public function testDoesHasFeature()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionRepositoryInterface::class);
        $subscription = $this->createMock(Subscription::class);
        $plan = $this->createMock(Plan::class);

        $planManager->method('getPlanByName')->with('plan_name')->willReturn($plan);
        $plan->method('getFeatures')->willReturn(['feature']);
        $subscription->method('getPlanName')->willReturn('plan_name');
        $subscriptionRepository->method('getAllActiveForCustomer')->with($customer)->willReturn([$subscription]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertTrue($subject->hasFeature($customer, 'feature'));
    }

    public function testDoesNotHaveLimit()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $subscriptionRepository->method('getAllActiveForCustomer')->with($customer)->willReturn([]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertEquals(0, $subject->getLimitCount($customer, 'feature'));
    }

    public function testDoesHaveLimit()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $planManager = $this->createMock(PlanManagerInterface::class);
        $subscriptionRepository = $this->createMock(SubscriptionRepositoryInterface::class);
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
        $subscriptionRepository->method('getAllActiveForCustomer')->with($customer)->willReturn([$subscription, $subscriptionTwo]);

        $subject = new CustomerPlanInfo($subscriptionRepository, $planManager);

        $this->assertEquals(10, $subject->getLimitCount($customer, 'feature'));
    }
}
