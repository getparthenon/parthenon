<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments;

use Parthenon\Payments\Exception\NoPriceFoundException;
use Parthenon\Subscriptions\Plan\Plan;

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
