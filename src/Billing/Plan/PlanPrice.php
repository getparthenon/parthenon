<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        return Money::of($this->amount, Currency::of(strtoupper($this->currency)));
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
