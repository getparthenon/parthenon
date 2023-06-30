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
