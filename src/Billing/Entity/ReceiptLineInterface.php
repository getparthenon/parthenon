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

interface ReceiptLineInterface
{
    public function getId();

    public function setId($id): void;

    public function getReceipt(): ReceiptInterface;

    public function setReceipt(ReceiptInterface $receipt): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getSubTotal(): int;

    public function setSubTotal(int $subTotal): void;

    public function getVatTotal(): int;

    public function setVatTotal(int $vatTotal): void;

    public function getDescription(): string;

    public function setDescription(?string $description): void;

    public function getTotalMoney(): Money;

    public function getVatTotalMoney(): Money;

    public function getSubTotalMoney(): Money;

    public function getVatPercentage(): ?float;

    public function setVatPercentage(?float $vatPercentage): void;
}
