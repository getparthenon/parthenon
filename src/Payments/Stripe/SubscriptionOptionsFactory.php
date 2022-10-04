<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Stripe;

use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\PriceProviderInterface;
use Parthenon\Payments\SubscriptionOptionsFactoryInterface;

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
