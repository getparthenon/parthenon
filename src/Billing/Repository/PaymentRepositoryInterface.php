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

namespace Parthenon\Billing\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;

interface PaymentRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Payment[]
     */
    public function getPaymentsForCustomerDuring(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentForReference(string $reference): Payment;

    /**
     * @return Payment[]
     */
    public function getPaymentsForCustomer(CustomerInterface $customer): array;

    /**
     * @return Payment[]
     */
    public function getPaymentsForSubscription(Subscription $subscription): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getLastPaymentForSubscription(Subscription $subscription): Payment;

    /**
     * @throws NoEntityFoundException
     */
    public function getLastPaymentForCustomer(CustomerInterface $customer): Payment;
}
