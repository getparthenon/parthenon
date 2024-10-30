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
