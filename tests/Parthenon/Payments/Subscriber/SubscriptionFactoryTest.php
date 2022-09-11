<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Subscriptions\Plan\Plan;
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
