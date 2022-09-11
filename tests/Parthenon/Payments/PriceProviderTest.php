<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments;

use Parthenon\Payments\Exception\NoPriceFoundException;
use Parthenon\Subscriptions\Plan\Plan;
use PHPUnit\Framework\TestCase;

class PriceProviderTest extends TestCase
{
    public function testThrowsExceptionIfNoPrice()
    {
        $this->expectException(NoPriceFoundException::class);
        $plan = $this->createMock(Plan::class);
        $plan->method('getName')->willReturn('basic');
        $priceProvider = new PriceProvider([]);
        $priceProvider->getPriceId($plan, 'yearly');
    }

    public function testThrowsExceptionIfPaymentSchedule()
    {
        $this->expectException(NoPriceFoundException::class);
        $plan = $this->createMock(Plan::class);
        $plan->method('getName')->willReturn('basic');
        $priceProvider = new PriceProvider(['basic' => ['yearly' => ['price_id' => 'price_id']]]);
        $priceProvider->getPriceId($plan, 'monthly');
    }

    public function testReturnsPriceId()
    {
        $plan = $this->createMock(Plan::class);
        $plan->method('getName')->willReturn('basic');
        $priceProvider = new PriceProvider(['basic' => ['yearly' => ['price_id' => 'price_id']]]);
        $this->assertEquals('price_id', $priceProvider->getPriceId($plan, 'yearly'));
    }
}
