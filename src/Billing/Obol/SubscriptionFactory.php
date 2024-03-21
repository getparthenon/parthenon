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

namespace Parthenon\Billing\Obol;

use Obol\Model\BillingDetails;
use Obol\Model\Subscription;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Plan\PlanPrice;

class SubscriptionFactory implements SubscriptionFactoryInterface
{
    public function __construct(
        private BillingDetailsFactoryInterface $billingDetailsFactory,
    ) {
    }

    public function createSubscription(
        BillingDetails $billingDetails,
        PlanPrice|Price $planPrice,
        int $seatNumbers,
        bool $hasTrial = false,
        int $trialLengthDays = 0,
    ): Subscription {
        $obolSubscription = new Subscription();
        $obolSubscription->setBillingDetails($billingDetails);
        $obolSubscription->setSeats($seatNumbers);
        $obolSubscription->setCostPerSeat($planPrice->getAsMoney());

        $obolSubscription->setHasTrial($hasTrial);
        $obolSubscription->setTrialLengthDays($trialLengthDays);

        if ($planPrice instanceof Price) {
            $obolSubscription->setPriceId($planPrice->getExternalReference());
        } elseif ($planPrice->hasPriceId()) {
            $obolSubscription->setPriceId($planPrice->getPriceId());
        }

        return $obolSubscription;
    }

    public function createSubscriptionFromEntity(\Parthenon\Billing\Entity\Subscription $subscription, bool $fullyBuilt = true): Subscription
    {
        $obolSubscription = new Subscription();

        if ($fullyBuilt) {
            $paymentDetails = $subscription->getPaymentDetails();
            $billingDetails = $this->billingDetailsFactory->createFromCustomerAndPaymentDetails($subscription->getCustomer(), $paymentDetails);
            $obolSubscription->setBillingDetails($billingDetails);
        }

        $obolSubscription->setId($subscription->getMainExternalReference());
        $obolSubscription->setLineId($subscription->getChildExternalReference());
        $obolSubscription->setCostPerSeat($subscription->getMoneyAmount());
        $obolSubscription->setSeats($subscription->getSeats());
        $obolSubscription->setHasTrial($subscription->getSubscriptionPlan()?->getHasTrial() ?? false);
        $obolSubscription->setTrialLengthDays($subscription->getSubscriptionPlan()?->getTrialLengthDays() ?? 0);

        return $obolSubscription;
    }
}
