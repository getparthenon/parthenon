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

use Obol\Model\Address as ObolAddress;
use Obol\Model\BillingDetails;
use Parthenon\Billing\Entity\CustomerInterface;

final class CustomerConverter implements CustomerConverterInterface
{
    public function convertToBillingDetails(CustomerInterface $customer): BillingDetails
    {
        $billingDetails = new BillingDetails();
        $billingDetails->setCustomerReference($customer->getExternalCustomerReference());

        $address = $this->buildAddresss($customer);

        $billingDetails->setAddress($address);
        $billingDetails->setEmail($customer->getBillingEmail());

        return $billingDetails;
    }

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
