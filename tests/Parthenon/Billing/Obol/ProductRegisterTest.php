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

namespace Parthenon\Billing\Obol;

use Monolog\Test\TestCase;
use Obol\Model\Product as ObolProduct;
use Obol\ProductServiceInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Product;

class ProductRegisterTest extends TestCase
{
    public function testDontCallObol()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $productService = $this->createMock(ProductServiceInterface::class);
        $provider->method('products')->willReturn($productService);

        $product = new Product();
        $product->setExternalReference('prod-id');

        $productService->expects($this->never())->method('createProduct');

        $subject = new ProductRegister($provider);
        $subject->registerProduct($product);
    }

    public function testDoCallObol()
    {
        $provider = $this->createMock(ProviderInterface::class);
        $productService = $this->createMock(ProductServiceInterface::class);
        $provider->method('products')->willReturn($productService);

        $product = new Product();
        $product->setName('prod-id');

        $productService->expects($this->once())->method('createProduct')->with($this->isInstanceOf(ObolProduct::class));

        $subject = new ProductRegister($provider);
        $subject->registerProduct($product);
    }
}
