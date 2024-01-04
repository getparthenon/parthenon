<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
