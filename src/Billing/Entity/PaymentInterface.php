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

use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Enum\PaymentStatus;

interface PaymentInterface
{
    public function getId();

    public function setId($id): void;

    public function getPaymentReference(): string;

    public function setPaymentReference(string $paymentReference): void;

    public function getProvider(): string;

    public function setProvider(string $provider): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): void;

    public function isRefunded(): bool;

    public function setRefunded(bool $refunded): void;

    public function isCompleted(): bool;

    public function setCompleted(bool $completed): void;

    public function isChargedBack(): bool;

    public function setChargedBack(bool $chargedBack): void;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void;

    public function getCustomer(): ?CustomerInterface;

    public function setCustomer(CustomerInterface $customer): void;

    public function hasCustomer(): bool;

    public function getMoneyAmount(): Money;

    public function setMoneyAmount(Money $money);

    public function getSubscription(): ?Subscription;

    public function setSubscription(?Subscription $subscription): void;

    public function getPaymentProviderDetailsUrl(): ?string;

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void;

    public function getSubscriptions(): Collection;

    public function setSubscriptions(Collection|array $subscriptions): void;

    public function addSubscription(Subscription $subscription): void;

    public function getStatus(): PaymentStatus;

    public function setStatus(PaymentStatus $status): void;

    public function getDescription(): ?string;

    public function setDescription(string $description): void;
}
