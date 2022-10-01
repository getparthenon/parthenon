<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
