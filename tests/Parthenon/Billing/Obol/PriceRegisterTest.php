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

namespace Parthenon\Billing\Obol;

use Obol\Model\CreatePrice;
use Obol\PriceServiceInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Product;
use PHPUnit\Framework\TestCase;

class PriceRegisterTest extends TestCase
{
    public function testDontCallObol()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $priceService = $this->createMock(PriceServiceInterface::class);
        $provider->method('prices')->willReturn($priceService);

        $price = new Price();
        $price->setExternalReference('place-holder');

        $priceService->expects($this->never())->method('createPrice')->with($price);

        $subject = new PriceRegister($provider);
        $subject->registerPrice($price);
    }

    public function testDoCallObol()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $priceService = $this->createMock(PriceServiceInterface::class);
        $provider->method('prices')->willReturn($priceService);

        $price = new Price();
        $price->setAmount(100);
        $price->setCurrency('GBP');
        $price->setSchedule('week');
        $price->setRecurring(true);

        $product = new Product();
        $product->setExternalReference('prod-id');
        $price->setProduct($product);

        $priceService->expects($this->once())->method('createPrice')->with($this->isInstanceOf(CreatePrice::class));

        $subject = new PriceRegister($provider);
        $subject->registerPrice($price);
    }
}
