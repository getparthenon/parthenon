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

use Obol\Model\Address as ObolAddress;
use Parthenon\Billing\Entity\CustomerInterface;

trait AddressTrait
{
    public function buildAddresss(CustomerInterface $customer): ObolAddress
    {
        $address = new ObolAddress();
        $address->setStreetLineOne($customer->getBillingAddress()->getStreetLineOne());
        $address->setStreetLineTwo($customer->getBillingAddress()->getStreetLineTwo());
        $address->setCity($customer->getBillingAddress()->getCity());
        $address->setState($customer->getBillingAddress()->getRegion());
        $address->setCountryCode($customer->getBillingAddress()->getCountry());
        $address->setPostalCode($customer->getBillingAddress()->getPostcode());

        return $address;
    }
}
