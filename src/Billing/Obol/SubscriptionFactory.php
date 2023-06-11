<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        $obolSubscription = new \Obol\Model\Subscription();
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
        $obolSubscription = new \Obol\Model\Subscription();

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
