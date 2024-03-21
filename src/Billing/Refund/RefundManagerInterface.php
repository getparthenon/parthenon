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

namespace Parthenon\Billing\Refund;

use Brick\Money\Money;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Entity\Subscription;

interface RefundManagerInterface
{
    public function issueRefundForPayment(Payment $payment, Money $amount, ?BillingAdminInterface $billingAdmin = null, ?string $reason = null): Refund;

    public function issueFullRefundForSubscription(Subscription $subscription, BillingAdminInterface $billingAdmin): Refund;

    public function issueProrateRefundForSubscription(Subscription $subscription, BillingAdminInterface $billingAdmin, \DateTimeInterface $start, \DateTimeInterface $end): Refund;
}
