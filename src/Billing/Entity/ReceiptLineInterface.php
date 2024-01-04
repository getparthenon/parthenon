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

    public function getVatPercentage(): float;

    public function setVatPercentage(float $vatPercentage): void;
}
