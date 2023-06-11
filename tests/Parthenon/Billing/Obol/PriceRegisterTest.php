<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
