<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Billing\Obol;

use Obol\Model\Events\AbstractCharge;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Factory\EntityFactoryInterface;

final class PaymentFactory implements PaymentFactoryInterface
{
    public function __construct(
        private CustomerProviderInterface $customerProvider,
        private ProviderInterface $provider,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function createFromPaymentDetails(PaymentDetails $paymentDetails, ?CustomerInterface $customer = null): Payment
    {
        $payment = $this->entityFactory->getPaymentEntity();
        $payment->setPaymentReference($paymentDetails->getPaymentReference());
        $payment->setPaymentProviderDetailsUrl($paymentDetails->getPaymentReferenceLink());
        $payment->setMoneyAmount($paymentDetails->getAmount());
        if (isset($customer)) {
            $payment->setCustomer($customer);
        }
        $payment->setCompleted(true);
        $payment->setCreatedAt(new \DateTime('now'));
        $payment->setUpdatedAt(new \DateTime('now'));
        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setProvider($this->provider->getName());

        return $payment;
    }

    public function fromSubscriptionCreation(PaymentDetails $paymentDetails, ?CustomerInterface $customer = null): Payment
    {
        if (!$customer) {
            $customer = $this->customerProvider->getCurrentCustomer();
        }

        $payment = $this->createFromPaymentDetails($paymentDetails, $customer);

        return $payment;
    }

    public function fromChargeEvent(AbstractCharge $charge): Payment
    {
        $payment = $this->entityFactory->getPaymentEntity();
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
