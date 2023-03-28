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

use Obol\Model\CreatePrice;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Price;

class PriceRegister implements PriceRegisterInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function registerPrice(Price $price): Price
    {
        if (!$price->hasExternalReference()) {
            $createPrice = new CreatePrice();
            $createPrice->setMoney($price->getAsMoney());
            $createPrice->setIncludingTax($price->isIncludingTax());
            $createPrice->setPaymentSchedule($price->getSchedule());
            $createPrice->setRecurring($price->isRecurring());
            $createPrice->setProductReference($price->getProduct()->getExternalReference());
            $creation = $this->provider->prices()->createPrice($createPrice);

            $price->setExternalReference($creation->getReference());
            $price->setPaymentProviderDetailsUrl($creation->getDetailsUrl());
        }

        return $price;
    }
}
