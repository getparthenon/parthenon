<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Billing\Plan;

use Parthenon\Billing\Exception\NoPlanPriceFoundException;

interface PlanInterface
{
    public function getName(): string;

    public function hasFeature(string $featureName): bool;

    public function getLimit(LimitableInterface $limitable): int;

    public function getLimits(): array;

    public function getPriceId(): ?string;

    public function setPriceId(string $priceId): void;

    /**
     * @throws NoPlanPriceFoundException
     */
    public function getPriceForPaymentSchedule(string $term, string $currency): PlanPrice;

    /**
     * @return PlanPrice[]
     */
    public function getPublicPrices(): array;

    public function getFeatures(): array;

    public function isFree(): bool;

    public function isPerSeat(): bool;

    public function getUserCount(): int;

    public function setPrices(array $prices): void;

    public function isPublic(): bool;

    public function getHasTrial(): ?bool;

    public function setHasTrial(?bool $hasTrial): void;

    public function getTrialLengthDays(): ?int;

    public function setTrialLengthDays(?int $trialLengthDays): void;

    public function hasEntityId(): bool;
}
