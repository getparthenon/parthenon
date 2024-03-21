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
