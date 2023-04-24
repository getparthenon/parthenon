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

use Brick\Money\Money;
use Monolog\Test\TestCase;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Common\Address;

class TaxCalculatorTest extends TestCase
{
    public function testGerman1199Vat()
    {
        $expected = 191;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(19);

        $amount = Money::ofMinor(1199, 'EUR');

        $subject = new TaxCalculator($countryRules);
        $actual = $subject->calculateVatAmountForCustomer($customer, $amount);

        $this->assertEquals($expected, $actual->getMinorAmount()->toInt());
    }

    public function testUk12000Vat()
    {
        $expected = 2000;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(20);

        $amount = Money::ofMinor(12000, 'EUR');

        $subject = new TaxCalculator($countryRules);
        $actual = $subject->calculateVatAmountForCustomer($customer, $amount);

        $this->assertEquals($expected, $actual->getMinorAmount()->toInt());
    }

    public function testCh1234500Vat()
    {
        $expected = 86128;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(7.5);

        $amount = Money::ofMinor(1234500, 'EUR');

        $subject = new TaxCalculator($countryRules);
        $actual = $subject->calculateVatAmountForCustomer($customer, $amount);

        $this->assertEquals($expected, $actual->getMinorAmount()->toInt());
    }
}
