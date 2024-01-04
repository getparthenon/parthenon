<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Obol;

use Obol\Model\BillingDetails;
use Obol\Model\Subscription;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Plan\PlanPrice;

interface SubscriptionFactoryInterface
{
    public function createSubscription(
        BillingDetails $billingDetails,
        PlanPrice|Price $planPrice,
        int $seatNumbers,
        bool $hasTrial = false,
        int $trialLengthDays = 0,
    ): Subscription;

    public function createSubscriptionFromEntity(\Parthenon\Billing\Entity\Subscription $subscription): Subscription;
}
