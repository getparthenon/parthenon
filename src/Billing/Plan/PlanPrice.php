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

namespace Parthenon\Billing\Plan;

use Brick\Money\Currency;
use Brick\Money\Money;

class PlanPrice
{
    public function __construct(
        private string $schedule,
        private string|int|float $amount,
        private string $currency,
        private ?string $priceId = null,
        private $entityId = null,
    ) {
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPriceId(): ?string
    {
        return $this->priceId;
    }

    public function hasPriceId(): bool
    {
        return isset($this->priceId);
    }

    public function getAsMoney(): Money
    {
        return Money::ofMinor($this->amount, Currency::of(strtoupper($this->currency)));
    }

    public function getEntityId(): mixed
    {
        return $this->entityId;
    }

    public function setEntityId(mixed $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function hasEntityId(): bool
    {
        return isset($this->entityId);
    }
}
