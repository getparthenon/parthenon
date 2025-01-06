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

namespace Parthenon\Billing\Tax;

use Monolog\Test\TestCase;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\ReceiptLine;
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

        $receiptLine = new ReceiptLine();
        $receiptLine->setTotal(1199);
        $receiptLine->setCurrency('EUR');

        $subject = new TaxCalculator($countryRules);
        $subject->calculateReceiptLine($customer, $receiptLine);
        $this->assertEquals($expected, $receiptLine->getVatTotalMoney()->getMinorAmount()->toInt());
    }

    public function testUk12000Vat()
    {
        $expected = 2000;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(20);

        $receiptLine = new ReceiptLine();
        $receiptLine->setTotal(12000);
        $receiptLine->setCurrency('EUR');

        $subject = new TaxCalculator($countryRules);
        $subject->calculateReceiptLine($customer, $receiptLine);

        $this->assertEquals($expected, $receiptLine->getVatTotalMoney()->getMinorAmount()->toInt());
    }

    public function testUk22345Vat()
    {
        $expected = 3724;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(20);

        $receiptLine = new ReceiptLine();
        $receiptLine->setTotal(22345);
        $receiptLine->setCurrency('EUR');

        $subject = new TaxCalculator($countryRules);
        $subject->calculateReceiptLine($customer, $receiptLine);

        $this->assertEquals($expected, $receiptLine->getVatTotalMoney()->getMinorAmount()->toInt());
    }

    public function testCh1234500Vat()
    {
        $expected = 86128;
        $address = new Address();
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $countryRules = $this->createMock(CountryRules::class);
        $countryRules->method('getDigitalVatPercentage')->with($address)->willReturn(7.5);

        $receiptLine = new ReceiptLine();
        $receiptLine->setTotal(1234500);
        $receiptLine->setCurrency('EUR');

        $subject = new TaxCalculator($countryRules);
        $subject->calculateReceiptLine($customer, $receiptLine);

        $this->assertEquals($expected, $receiptLine->getVatTotalMoney()->getMinorAmount()->toInt());
    }
}
