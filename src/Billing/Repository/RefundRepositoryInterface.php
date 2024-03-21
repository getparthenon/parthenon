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

namespace Parthenon\Billing\Repository;

use Brick\Money\Money;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Common\Exception\NoEntityFoundException;

interface RefundRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Refund[]
     */
    public function getForBillingAdmin(BillingAdminInterface $billingAdmin): array;

    /**
     * @return Refund[]
     */
    public function getForCustomer(CustomerInterface $customer): array;

    /**
     * @return Refund[]
     */
    public function getForPayment(Payment $payment): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getForExternalReference(string $externalReference): Refund;

    public function getTotalRefundedForPayment(Payment $payment): Money;
}
