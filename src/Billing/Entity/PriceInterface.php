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
use Parthenon\Athena\Entity\DeletableInterface;

interface PriceInterface
{
    public function getId();

    public function setId($id): void;

    public function getAmount(): ?int;

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
