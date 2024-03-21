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

namespace Parthenon\Billing\Repository\Orm;

use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class RefundRepository extends DoctrineCrudRepository implements RefundRepositoryInterface
{
    public function getForBillingAdmin(BillingAdminInterface $billingAdmin): array
    {
        return $this->entityRepository->findBy(['billingAdmin' => $billingAdmin]);
    }

    public function getForCustomer(CustomerInterface $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }

    public function getForPayment(Payment $payment): array
    {
        return $this->entityRepository->findBy(['payment' => $payment]);
    }

    public function getTotalRefundedForPayment(Payment $payment): Money
    {
        $refunds = $this->getForPayment($payment);

        $amount = Money::of(0, Currency::of($payment->getCurrency()));

        foreach ($refunds as $refund) {
            $amount = $amount->plus($refund->getAsMoney());
        }

        return $amount;
    }

    public function getForExternalReference(string $externalReference): Refund
    {
        $refund = $this->entityRepository->findOneBy(['externalReference' => $externalReference]);

        if (!$refund instanceof Refund) {
            throw new NoEntityFoundException(sprintf("Can't find refund for external reference '%s'", $externalReference));
        }

        return $refund;
    }
}
