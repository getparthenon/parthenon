<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Entity;

use Doctrine\Common\Collections\Collection;

interface SubscriptionPlanInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id): void;

    public function isPublic(): bool;

    public function setPublic(bool $public): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getCodeName(): ?string;

    public function setCodeName(?string $codeName): void;

    /**
     * @return string
     */
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
