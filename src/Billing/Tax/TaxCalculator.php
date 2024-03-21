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

namespace Parthenon\Billing\Tax;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\ReceiptLineInterface;

class TaxCalculator implements TaxCalculatorInterface
{
    public function __construct(private CountryRulesInterface $rules)
    {
    }

    public function calculateReceiptLine(CustomerInterface $customer, ReceiptLineInterface $receiptLine): void
    {
        $money = Money::ofMinor($receiptLine->getTotal(), strtoupper($receiptLine->getCurrency()));
        $rawRate = $this->rules->getDigitalVatPercentage($customer->getBillingAddress());

        $rate = ($rawRate / 100) + 1;

        $subTotal = $money->dividedBy($rate, RoundingMode::HALF_UP);
        $vat = $money->minus($subTotal, RoundingMode::HALF_DOWN);

        $receiptLine->setVatPercentage(floatval($rawRate));
        $receiptLine->setSubTotal($subTotal->getMinorAmount()->toInt());
        $receiptLine->setVatTotal($vat->getMinorAmount()->toInt());
    }
}
