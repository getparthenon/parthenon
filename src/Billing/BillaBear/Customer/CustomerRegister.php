<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Billing\BillaBear\Customer;

use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Customer\CustomerRegisterInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Common\Address;

class CustomerRegister implements CustomerRegisterInterface
{
    public function __construct(
        private SdkFactory $sdkFactory,
    ) {
    }

    public function createCustomer(CustomerInterface $customer): void
    {
        $payload = $this->buildCreatePayload($customer);
        $response = $this->sdkFactory->createCustomersApi()->createCustomer($payload);
        $customer->setExternalCustomerReference($response->getId());
    }

    public function updateCustomer(CustomerInterface $customer): void
    {
        $payload = $this->buildCreatePayload($customer);
        $this->sdkFactory->createCustomersApi()->updateCustomer($payload, $customer->getExternalCustomerReference());
    }

    private function buildCreatePayload(CustomerInterface $internalCustomer): \BillaBear\Model\Customer
    {
        $customer = new \BillaBear\Model\Customer();
        $customer->setEmail($internalCustomer->getBillingEmail());
        if ($internalCustomer->hasBillingAddress()) {
            $customer->setAddress($this->convertBillingAddress($internalCustomer->getBillingAddress()));
        }

        return $customer;
    }

    private function convertBillingAddress(Address $address): \BillaBear\Model\Address
    {
        $apiAddress = new \BillaBear\Model\Address();
        $apiAddress->setStreetLineOne($address->getStreetLineOne());
        $apiAddress->setStreetLineTwo($address->getStreetLineTwo());
        $apiAddress->setCity($address->getCity());
        $apiAddress->setCountry($address->getCountry());
        $apiAddress->setPostcode($address->getPostcode());

        return $apiAddress;
    }
}
