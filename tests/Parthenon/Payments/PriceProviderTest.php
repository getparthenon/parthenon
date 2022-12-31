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

namespace Parthenon\Payments;

use Parthenon\Payments\Exception\NoPriceFoundException;
use Parthenon\Payments\Plan\Plan;
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
