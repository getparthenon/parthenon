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
