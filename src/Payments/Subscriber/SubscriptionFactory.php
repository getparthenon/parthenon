<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Plan\Plan;
use Parthenon\Subscriptions\Subscriber\SubscriptionFactoryInterface;

final class SubscriptionFactory implements SubscriptionFactoryInterface
{
    public function __construct(private PriceProviderInterface $priceProvider)
    {
    }

    public function createFromPlanAndPaymentSchedule(Plan $plan, string $paymentSchedule): Subscription
    {
        $priceId = $this->priceProvider->getPriceId($plan, $paymentSchedule);

        $subscription = new Subscription();
        $subscription->setPlanName($plan->getName());
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setPriceId($priceId);

        return $subscription;
    }
}
