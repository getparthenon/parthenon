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
use Parthenon\Athena\Entity\DeletableInterface;

interface PriceInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getExternalReference(): ?string;

    public function setExternalReference(?string $externalReference): void;

    public function hasExternalReference(): bool;

    public function isRecurring(): bool;

    public function setRecurring(bool $recurring): void;

    public function getSchedule(): ?string;

    public function setSchedule(?string $schedule): void;

    public function isIncludingTax(): bool;

    public function setIncludingTax(bool $includingTax): void;

    public function getAsMoney(): Money;

    public function getProduct(): Product;

    public function setProduct(Product $product): void;

    public function isPublic(): bool;

    public function setPublic(?bool $public): void;

    public function getPaymentProviderDetailsUrl(): ?string;

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void;

    public function getDisplayName(): string;

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface;

    public function isDeleted(): bool;

    public function markAsDeleted(): DeletableInterface;

    public function unmarkAsDeleted(): DeletableInterface;

    public function getIsDeleted(): ?bool;

    public function setIsDeleted(?bool $isDeleted): void;

    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): void;
}
