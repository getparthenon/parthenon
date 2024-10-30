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

namespace Parthenon\Billing\BillaBear\PaymentMethod;

use BillaBear\Model\FrontendToken;
use BillaBear\Model\PaymentDetails;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;

class FrontendAddProcessor implements FrontendAddProcessorInterface
{
    public function __construct(private SdkFactory $sdkFactory)
    {
    }

    public function startTokenProcess(CustomerInterface $customer): string
    {
        $response = $this->sdkFactory->createPaymentDetails()->startFrontendPaymentDetails($customer->getExternalCustomerReference());

        return $response->getToken();
    }

    public function createPaymentDetailsFromToken(CustomerInterface $customer, string $token): PaymentCard
    {
        $token = new FrontendToken(['token' => $token]);

        $response = $this->sdkFactory->createPaymentDetails()->completeFrontendPaymentDetails($token, $customer->getExternalCustomerReference());

        return $this->buildPaymentCard($response);
    }

    private function buildPaymentCard(PaymentDetails $paymentDetails): PaymentCard
    {
        $paymentCard = new PaymentCard();
        $paymentCard->setId($paymentDetails->getId());
        $paymentCard->setName($paymentDetails->getName());
        $paymentCard->setDefaultPaymentOption($paymentDetails->getDefault());
        $paymentCard->setBrand($paymentDetails->getBrand());
        $paymentCard->setLastFour($paymentDetails->getLastFour());
        $paymentCard->setExpiryMonth($paymentDetails->getExpiryMonth());
        $paymentCard->setExpiryYear($paymentDetails->getExpiryYear());
        $paymentCard->setCreatedAt(new \DateTime($paymentDetails->getCreatedAt()));

        return $paymentCard;
    }
}
