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
