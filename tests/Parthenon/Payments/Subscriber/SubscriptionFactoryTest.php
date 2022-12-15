<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\PriceProviderInterface;
use PHPUnit\Framework\TestCase;

class SubscriptionFactoryTest extends TestCase
{
    public function testReturnsASubscriptionCorrectlyFilled()
    {
        $plan = $this->createMock(Plan::class);
        $priceProvder = $this->createMock(PriceProviderInterface::class);

        $paymentSchedule = 'monthly';
        $plan->method('getName')->willReturn('plan_name');
        $priceProvder->method('getPriceId')->with($plan, $paymentSchedule)->willReturn('price_id');

        $factory = new SubscriptionFactory($priceProvder);
        $subscription = $factory->createFromPlanAndPaymentSchedule($plan, $paymentSchedule);

        $this->assertEquals('plan_name', $subscription->getPlanName());
        $this->assertEquals('price_id', $subscription->getPriceId());
        $this->assertEquals($paymentSchedule, $subscription->getPaymentSchedule());
    }
}
