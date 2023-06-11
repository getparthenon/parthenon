<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\PaymentMethod;

use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;

final class DefaultPaymentManager implements DefaultPaymentManagerInterface
{
    public function __construct(
        private PaymentCardRepositoryInterface $paymentDetailsRepository,
        private ProviderInterface $provider,
        private BillingDetailsFactoryInterface $billingDetailsFactory,
    ) {
    }

    public function makePaymentDetailsDefault(CustomerInterface $customer, PaymentCard $paymentDetails): void
    {
        $this->paymentDetailsRepository->markAllCustomerCardsAsNotDefault($customer);
        $paymentDetails = $this->paymentDetailsRepository->findById($paymentDetails->getId());
        $paymentDetails->setDefaultPaymentOption(true);
        $this->paymentDetailsRepository->save($paymentDetails);

        $obolBillingDetails = $this->billingDetailsFactory->createFromCustomerAndPaymentDetails($customer, $paymentDetails);
        $this->provider->payments()->makeCardDefault($obolBillingDetails);
    }
}
