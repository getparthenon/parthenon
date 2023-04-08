<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Subscription;

use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanPrice;

interface SubscriptionManagerInterface
{
    public function startSubscription(CustomerInterface $customer, SubscriptionPlan|Plan $plan, Price|PlanPrice $planPrice, PaymentDetails $paymentDetails, int $seatNumbers): Subscription;

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription;

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription;

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription;

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTime $dateTime): Subscription;
}
