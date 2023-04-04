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

namespace Parthenon\Billing\Obol;

use Obol\Model\BillingDetails;
use Obol\Model\Subscription;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Plan\PlanPrice;

class SubscriptionFactory implements SubscriptionFactoryInterface
{
    public function createSubscription(
        BillingDetails $billingDetails,
        PlanPrice $planPrice,
        int $seatNumbers,
    ): Subscription {
        $obolSubscription = new \Obol\Model\Subscription();
        $obolSubscription->setBillingDetails($billingDetails);
        $obolSubscription->setSeats($seatNumbers);
        $obolSubscription->setCostPerSeat($planPrice->getPriceAsMoney());
        if ($planPrice->hasPriceId()) {
            $obolSubscription->setPriceId($planPrice->getPriceId());
        }

        return $obolSubscription;
    }

    public function createSubscriptionWithPrice(BillingDetails $billingDetails, Price $price, int $seatNumbers): Subscription
    {
        $obolSubscription = new \Obol\Model\Subscription();
        $obolSubscription->setBillingDetails($billingDetails);
        $obolSubscription->setSeats($seatNumbers);
        $obolSubscription->setCostPerSeat($price->getAsMoney());
        $obolSubscription->setPriceId($price->getExternalReference());

        return $obolSubscription;
    }
}
