<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
