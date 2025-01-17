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

use Obol\Model\Product as ObolProduct;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Product;

class ProductRegister implements ProductRegisterInterface
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    public function registerProduct(Product $product): Product
    {
        if (!$product->hasExternalReference()) {
            $obolProduct = new ObolProduct();
            $obolProduct->setName($product->getName());

            $productCreation = $this->provider->products()->createProduct($obolProduct);
            $product->setExternalReference($productCreation->getReference());
            $product->setPaymentProviderDetailsUrl($productCreation->getDetailsUrl());
        }

        return $product;
    }
}
