<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
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

class TaxCalculator implements TaxCalculatorInterface
{
    public function __construct(private CountryRulesInterface $rules)
    {
    }

    public function calculateSubTotalForCustomer(CustomerInterface $customer, Money $money): Money
    {
        $rate = $this->rules->getDigitalVatPercentage($customer->getBillingAddress());

        $rate = ($rate / 100) + 1;

        return $money->dividedBy($rate, RoundingMode::HALF_DOWN);
    }

    public function calculateVatAmountForCustomer(CustomerInterface $customer, Money $money): Money
    {
        return $money->minus($this->calculateSubTotalForCustomer($customer, $money), RoundingMode::HALF_UP);
    }
}
