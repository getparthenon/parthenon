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

namespace Parthenon\Billing\Subscription;

use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanPrice;

interface SubscriptionManagerInterface
{
    public function startSubscription(CustomerInterface $customer, SubscriptionPlan|Plan $plan, Price|PlanPrice $planPrice, ?PaymentCard $paymentDetails = null, int $seatNumbers = 1, ?bool $hasTrial = null, ?int $trialLengthDays = 0): Subscription;

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription;

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription;

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription;

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription;

    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void;

    public function changeSubscriptionPlan(Subscription $subscription, SubscriptionPlan $plan, Price $price, BillingChangeTiming $billingChangeTiming): void;
}
