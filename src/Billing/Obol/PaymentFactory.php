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

use Obol\Model\Events\AbstractCharge;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Enum\PaymentStatus;

final class PaymentFactory implements PaymentFactoryInterface
{
    public function __construct(
        private CustomerProviderInterface $customerProvider,
        private ProviderInterface $provider,
    ) {
    }

    public function fromSubscriptionCreation(PaymentDetails $paymentDetails, ?CustomerInterface $customer = null): Payment
    {
        if (!$customer) {
            $customer = $this->customerProvider->getCurrentCustomer();
        }

        $payment = new Payment();
        $payment->setPaymentReference($paymentDetails->getPaymentReference());
        $payment->setPaymentProviderDetailsUrl($paymentDetails->getPaymentReferenceLink());
        $payment->setMoneyAmount($paymentDetails->getAmount());
        $payment->setCustomer($customer);
        $payment->setCompleted(true);
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setProvider($this->provider->getName());

        return $payment;
    }

    public function fromChargeEvent(AbstractCharge $charge): Payment
    {
        $payment = new Payment();
        $payment->setPaymentReference($charge->getPaymentReference());
        $payment->setPaymentProviderDetailsUrl($charge->getDetailsLink());
        $payment->setAmount($charge->getAmount());
        $payment->setCurrency($charge->getCurrency());
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setProvider($this->provider->getName());

        return $payment;
    }
}
