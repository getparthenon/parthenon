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

use Obol\Model\BillingDetails;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentDetails;

class BillingDetailsFactory
{
    use AddressTrait;

    public function createFromCustomerAndPaymentDetails(
        CustomerInterface $customer,
        PaymentDetails $paymentDetails
    ): BillingDetails {
        $address = $this->buildAddresss($customer);
        $billingDetails = new BillingDetails();
        $billingDetails->setCustomerReference($customer->getExternalCustomerReference());
        $billingDetails->setStoredPaymentReference($paymentDetails->getStoredPaymentReference());
        $billingDetails->setAddress($address);

        return $billingDetails;
    }
}
