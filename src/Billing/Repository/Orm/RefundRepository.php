<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    /**
     * {@inheritDoc}
     */
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
