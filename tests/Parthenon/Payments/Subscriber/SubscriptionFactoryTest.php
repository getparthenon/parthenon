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
