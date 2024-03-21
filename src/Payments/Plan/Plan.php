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

namespace Parthenon\Payments\Plan;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\ParameterNotSetException;

final class Plan
{
    public const PAY_YEARLY = 'yearly';
    public const PAY_MONTHLY = 'monthly';
    public const CHECK_FEATURE = 'feature';

    public function __construct(
        private string $name,
        private array $limits,
        private array $features,
        private string $yearlyPriceId,
        private string $monthlyPriceId,
        private bool $isFree,
        private bool $isPerSeat,
        private int $userCount,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasFeature(string $featureName): bool
    {
        return in_array($featureName, $this->features);
    }

    public function getLimit(LimitableInterface $limitable): int
    {
        foreach ($this->limits as $name => $limit) {
            if ($limitable->getLimitableName() === $name) {
                if (!isset($limit['limit'])) {
                    throw new ParameterNotSetException('The limit is not set correctly');
                }

                return $limit['limit'];
            }
        }

        return -1;
    }

    public function getLimits(): array
    {
        return $this->limits;
    }

    public function getYearlyPriceId(): string
    {
        return $this->yearlyPriceId;
    }

    public function setYearlyPriceId(string $yearlyPriceId): void
    {
        $this->yearlyPriceId = $yearlyPriceId;
    }

    public function getMonthlyPriceId(): string
    {
        return $this->monthlyPriceId;
    }

    public function setMonthlyPriceId(string $monthlyPriceId): void
    {
        $this->monthlyPriceId = $monthlyPriceId;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getPriceIdForPaymentSchedule(string $term): string
    {
        switch ($term) {
            case static::PAY_YEARLY:
                return $this->yearlyPriceId;
            case static::PAY_MONTHLY:
                return $this->monthlyPriceId;
            default:
                throw new GeneralException('Only yearly or monthly are currently supported');
        }
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function isPerSeat(): bool
    {
        return $this->isPerSeat;
    }

    public function getUserCount(): int
    {
        return $this->userCount;
    }
}
