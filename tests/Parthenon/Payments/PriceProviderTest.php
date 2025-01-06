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
