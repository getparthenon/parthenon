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
