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

namespace Parthenon\Billing\Factory;

use Obol\Model\CardFile;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;

final class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    public function buildFromCardFile(CustomerInterface $customer, CardFile $cardFile, string $provider): PaymentCard
    {
        $paymentDetails = new PaymentCard();
        $paymentDetails->setCustomer($customer);
        $paymentDetails->setStoredCustomerReference($customer->getExternalCustomerReference());
        $paymentDetails->setStoredPaymentReference($cardFile->getStoredPaymentReference());
        $paymentDetails->setProvider($provider);
        $paymentDetails->setDefaultPaymentOption(true);
        $paymentDetails->setName('Default');
        $paymentDetails->setBrand($cardFile->getBrand());
        $paymentDetails->setLastFour($cardFile->getLastFour());
        $paymentDetails->setExpiryMonth($cardFile->getExpiryMonth());
        $paymentDetails->setExpiryYear($cardFile->getExpiryYear());
        $paymentDetails->setDeleted(false);
        $paymentDetails->setCreatedAt(new \DateTime());

        return $paymentDetails;
    }
}
