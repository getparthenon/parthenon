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

namespace Parthenon\Billing\BillaBear\Repository;

use BillaBear\Model\PaymentDetails;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;

class PaymentCardRepository implements PaymentCardRepositoryInterface
{
    public function __construct(private SdkFactory $sdkFactory)
    {
    }

    public function getPaymentCardForCustomer(CustomerInterface $customer): array
    {
        $cardResponse = $this->sdkFactory->createPaymentDetails()->listPaymentDetails($customer->getExternalCustomerReference());

        $output = [];

        foreach ($cardResponse->getData() as $paymentDetails) {
            $output[] = $this->buildPaymentCard($paymentDetails);
        }

        return $output;
    }

    public function markAllCustomerCardsAsNotDefault(CustomerInterface $customer): void
    {
        // TODO: Implement markAllCustomerCardsAsNotDefault() method.
    }

    public function getDefaultPaymentCardForCustomer(CustomerInterface $customer): PaymentCard
    {
        // TODO: Implement getDefaultPaymentCardForCustomer() method.
    }

    public function findById($id)
    {
        $paymentCard = $this->sdkFactory->createPaymentDetails()->getPaymentDetails($id);

        return $this->buildPaymentCard($paymentCard);
    }

    public function save($entity)
    {
        throw new \Exception("SHouldn't be used. ISP failure.");
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
