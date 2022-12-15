<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments;

use Parthenon\Payments\Exception\NoPriceFoundException;
use Parthenon\Payments\Plan\Plan;

final class PriceProvider implements PriceProviderInterface
{
    public function __construct(private array $prices)
    {
    }

    public function getPriceId(Plan $plan, string $paymentSchedule): string
    {
        $name = $plan->getName();

        if (isset($this->prices[$name]) && isset($this->prices[$name][$paymentSchedule])) {
            return $this->prices[$name][$paymentSchedule]['price_id'];
        }

        throw new NoPriceFoundException();
    }

    public function getTrial(Plan $plan, string $paymentSchedule): int
    {
        $name = $plan->getName();

        if (isset($this->prices[$name]) && isset($this->prices[$name][$paymentSchedule])) {
            return $this->prices[$name][$paymentSchedule]['trial_day_length'] ?? 0;
        }

        throw new NoPriceFoundException();
    }

    public function getPrices(Plan $plan): array
    {
        $prices = [];

        if (isset($this->prices[$plan->getName()])) {
            foreach ($this->prices[$plan->getName()] as $paymentSchedule => $priceData) {
                $prices[$paymentSchedule] = $priceData['price'];
            }
        }

        return $prices;
    }
}
