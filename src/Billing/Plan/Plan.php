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

use Parthenon\Billing\Exception\NoPlanPriceFoundException;
use Parthenon\Common\Exception\ParameterNotSetException;

final class Plan implements PlanInterface
{
    public const PAY_YEARLY = 'yearly';
    public const PAY_MONTHLY = 'monthly';
    public const CHECK_FEATURE = 'feature';

    public function __construct(
        private string $name,
        private array $limits,
        private array $features,
        private array $prices,
        private bool $isFree,
        private bool $isPerSeat,
        private int $userCount,
        private bool $public = false,
        private ?bool $hasTrial = false,
        private ?int $trialLengthDays = 0,
        private mixed $entityId = null,
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

    public function getPriceId(): ?string
    {
        return $this->priceId;
    }

    public function setPriceId(string $priceId): void
    {
        $this->priceId = $priceId;
    }

    /**
     * @throws NoPlanPriceFoundException
     */
    public function getPriceForPaymentSchedule(string $term, string $currency): PlanPrice
    {
        if (!isset($this->prices[$term][$currency])) {
            throw new NoPlanPriceFoundException(sprintf("No currency '%s' found for '%s' schedule found", $currency, $term));
        }

        return new PlanPrice($term, $this->prices[$term][$currency]['amount'], $currency, $this->prices[$term][$currency]['price_id'] ?? null, $this->prices[$term][$currency]['entity_id'] ?? null);
    }

    /**
     * @return PlanPrice[]
     */
    public function getPublicPrices(): array
    {
        $output = [];
        foreach ($this->prices as $term => $currencyData) {
            foreach ($currencyData as $currency => $data) {
                if (!$data['public']) {
                    continue;
                }
                $output[] = new PlanPrice($term, $data['amount'], $currency, $data['price_id'] ?? null, $data['entity_id'] ?? null);
            }
        }

        return $output;
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

    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function getHasTrial(): bool
    {
        return true === $this->hasTrial;
    }

    public function setHasTrial(?bool $hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    public function getTrialLengthDays(): int
    {
        return (int) $this->trialLengthDays;
    }

    public function setTrialLengthDays(?int $trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
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
