<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Tax;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\ReceiptLine;

class TaxCalculator implements TaxCalculatorInterface
{
    public function __construct(private CountryRulesInterface $rules)
    {
    }

    public function calculateReceiptLine(CustomerInterface $customer, ReceiptLine $receiptLine): void
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
