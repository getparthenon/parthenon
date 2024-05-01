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
use Parthenon\Common\Address;

interface ReceiptInterface
{
    public function getId();

    public function setId($id): void;

    public function getInvoiceNumber(): ?string;

    public function setInvoiceNumber(?string $invoiceNumber): void;

    public function isValid(): bool;

    public function setValid(bool $valid): void;

    public function getBillerAddress(): Address;

    public function setBillerAddress(Address $billerAddress): void;

    public function getPayeeAddress(): Address;

    public function setPayeeAddress(Address $payeeAddress): void;

    public function getCustomer(): CustomerInterface;

    public function setCustomer(CustomerInterface $customer): void;

    public function addPayment(Payment $payment): void;

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection|array;

    public function setPayments(Collection|array $payments): void;

    public function getComment(): ?string;

    public function setComment(?string $comment): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getSubTotal(): int;

    public function setSubTotal(int $subTotal): void;

    public function getVatTotal(): int;

    public function setVatTotal(int $vatTotal): void;

    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): void;

    /**
     * @return Collection|ReceiptLine[]
     */
    public function getLines(): Collection|array;

    public function setLines(Collection|array $lines): void;

    /**
     * @return Subscription[]|Collection
     */
    public function getSubscriptions(): Collection|array;

    public function setSubscriptions(Collection|array $subscriptions): void;

    public function getTotalMoney(): Money;

    public function getVatTotalMoney(): Money;

    public function getSubTotalMoney(): Money;

    public function getVatPercentage(): ?float;

    public function setVatPercentage(?float $vatPercentage): void;
}
