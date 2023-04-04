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

use Obol\Model\SubscriptionCreationResponse;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;

class PaymentFactory implements PaymentFactoryInterface
{
    public function __construct(
        private CustomerProviderInterface $customerProvider,
        private ProviderInterface $provider,
    ) {
    }

    public function fromSubscriptionCreation(SubscriptionCreationResponse $subscriptionCreationResponse, ?CustomerInterface $customer = null): Payment
    {
        if (!$customer) {
            $customer = $this->customerProvider->getCurrentCustomer();
        }

        $payment = new Payment();
        $payment->setPaymentReference($subscriptionCreationResponse->getPaymentDetails()->getPaymentReference());
        $payment->setMoneyAmount($subscriptionCreationResponse->getPaymentDetails()->getAmount());
        $payment->setCustomer($customer);
        $payment->setCompleted(true);
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setProvider($this->provider->getName());

        return $payment;
    }
}
