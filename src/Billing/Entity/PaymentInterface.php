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
