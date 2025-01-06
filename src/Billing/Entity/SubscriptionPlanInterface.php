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

namespace Parthenon\Billing\Entity;

use Doctrine\Common\Collections\Collection;

interface SubscriptionPlanInterface
{
    public function getId();

    public function setId($id): void;

    public function isPublic(): bool;

    public function setPublic(bool $public): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getCodeName(): ?string;

    public function setCodeName(?string $codeName): void;

    public function getExternalReference(): ?string;

    public function setExternalReference(string $externalReference): void;

    public function hasExternalReference(): bool;

    public function getPaymentProviderDetailsLink(): ?string;

    public function setPaymentProviderDetailsLink(?string $paymentProviderDetailsLink): void;

    public function getLimits(): Collection|array;

    /**
     * @param SubscriptionPlanLimit[]|Collection $limits
     */
    public function setLimits(Collection|array $limits): void;

    public function addLimit(SubscriptionPlanLimit $limit): void;

    public function removeLimit(SubscriptionPlanLimit $limit): void;

    public function addFeature(SubscriptionFeature $subscriptionFeature): void;

    public function removeFeature(SubscriptionFeature $subscriptionFeature): void;

    public function addPrice(Price $price): void;

    public function getPriceForCurrencyAndSchedule(string $currency, string $schedule): Price;

    public function removePrice(Price $price): void;

    public function getPrices(): Collection|array;

    public function setPrices(Collection|array $prices): void;

    public function getDisplayName(): string;

    public function isPerSeat(): bool;

    public function setPerSeat(bool $perSeat): void;

    public function isFree(): bool;

    public function setFree(bool $free): void;

    public function getUserCount(): int;

    public function setUserCount(int $userCount): void;

    public function getFeatures(): Collection|array;

    public function setFeatures(Collection|array $features): void;

    public function getProduct(): ProductInterface;

    public function setProduct(ProductInterface $product): void;

    public function getHasTrial(): bool;

    public function setHasTrial(?bool $hasTrial): void;

    public function getTrialLengthDays(): int;

    public function setTrialLengthDays(?int $trialLengthDays): void;
}
