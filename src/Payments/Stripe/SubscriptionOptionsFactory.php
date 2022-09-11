<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Stripe;

use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Subscriptions\Plan\Plan;
use Parthenon\Subscriptions\SubscriptionOptionsFactoryInterface;

class SubscriptionOptionsFactory implements SubscriptionOptionsFactoryInterface
{
    public function __construct(private PriceProviderInterface $priceProvider)
    {
    }

    public function getOptions(Plan $plan, string $paymentSchedule): array
    {
        $output = [];
        $trial = $this->priceProvider->getTrial($plan, $paymentSchedule);
        if ($trial > 0) {
            $output['subscription_data'] = ['trial_period_days' => $trial];
        }

        return $output;
    }
}
