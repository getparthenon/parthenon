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
