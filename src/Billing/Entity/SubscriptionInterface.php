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

namespace Parthenon\Billing\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Enum\SubscriptionStatus;

interface SubscriptionInterface
{
    public function getId();

    public function setId($id): void;

    public function getPlanName(): string;

    public function setPlanName(string $planName): void;

    public function getPaymentSchedule(): ?string;

    public function setPaymentSchedule(?string $paymentSchedule): void;

    public function getSeats(): ?int;

    public function setSeats(?int $seats): void;

    public function getStatus(): SubscriptionStatus;

    public function setStatus(SubscriptionStatus $status): void;

    public function getSubscriptionPlan(): ?SubscriptionPlan;

    public function setSubscriptionPlan(?SubscriptionPlan $subscriptionPlan): void;

    public function getMainExternalReference(): ?string;

    public function setMainExternalReference(string $mainExternalReference): void;

    public function getChildExternalReference(): ?string;

    public function setChildExternalReference(?string $childExternalReference): void;

    public function getAmount(): ?int;

    public function setAmount(?int $amount): void;

    public function getCurrency(): ?string;

    public function setCurrency(?string $currency): void;

    public function getPrice(): ?Price;

    public function setPrice(?Price $price): void;

    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): void;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void;

    public function getEndedAt(): ?\DateTimeInterface;

    public function setEndedAt(?\DateTimeInterface $endedAt): void;

    public function endAtEndOfPeriod(): void;

    public function endNow(): void;

    public function isActive(): bool;

    public function setActive(bool $active): void;

    public function getValidUntil(): ?\DateTime;

    public function setValidUntil(\DateTime $validUntil): void;

    public function getMoneyAmount(): Money;

    public function setMoneyAmount(?Money $money);

    public function getCustomer(): CustomerInterface;

    public function setCustomer(CustomerInterface $customer): void;

    public function getMainExternalReferenceDetailsUrl(): ?string;

    public function setMainExternalReferenceDetailsUrl(?string $mainExternalReferenceDetailsUrl): void;

    public function getStartOfCurrentPeriod(): ?\DateTimeInterface;

    public function setStartOfCurrentPeriod(?\DateTimeInterface $startOfCurrentPeriod): void;

    public function isHasTrial(): bool;

    public function setHasTrial(bool $hasTrial): void;

    public function getTrialLengthDays(): ?int;

    public function setTrialLengthDays(?int $trialLengthDays): void;

    public function getPaymentDetails(): ?PaymentCard;

    public function setPaymentDetails(?PaymentCard $paymentDetails): void;

    public function getPayments(): Collection;

    public function setPayments(Collection $payments): void;
}
