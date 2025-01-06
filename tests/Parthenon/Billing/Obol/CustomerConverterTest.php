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

namespace Parthenon\Billing\Obol;

use Obol\Model\BillingDetails;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;

class CustomerConverterTest extends TestCase
{
    public const EMAIL = 'iain.cambridge@example.org';
    public const CUSTOMER_REFERENCE = 'a-reference';
    public const STREET_LINE_ONE = '1 Example Way';
    public const EXAMPLE_TOWN = 'Example Town';
    public const REGION = 'Example';
    public const COUNTRY_CODE = 'US';
    public const POST_CODE = 'GE384';

    public function testConvertCustomer()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $address = $this->createMock(Address::class);

        $customer->method('getBillingEmail')->willReturn(self::EMAIL);
        $customer->method('getExternalCustomerReference')->willReturn(self::CUSTOMER_REFERENCE);
        $customer->method('getBillingAddress')->willReturn($address);

        $address->method('getStreetLineOne')->willReturn(self::STREET_LINE_ONE);
        $address->method('getCity')->willReturn(self::EXAMPLE_TOWN);
        $address->method('getRegion')->willReturn(self::REGION);
        $address->method('getCountry')->willReturn(self::COUNTRY_CODE);
        $address->method('getPostcode')->willReturn(self::POST_CODE);

        $subject = new CustomerConverter();
        $actual = $subject->convertToBillingDetails($customer);
        $this->assertInstanceOf(BillingDetails::class, $actual);

        $this->assertEquals(self::EMAIL, $actual->getEmail());
        $this->assertEquals(self::CUSTOMER_REFERENCE, $actual->getCustomerReference());
        $this->assertEquals(self::STREET_LINE_ONE, $actual->getAddress()->getStreetLineOne());
        $this->assertEquals(self::EXAMPLE_TOWN, $actual->getAddress()->getCity());
        $this->assertEquals(self::REGION, $actual->getAddress()->getState());
        $this->assertEquals(self::COUNTRY_CODE, $actual->getAddress()->getCountryCode());
        $this->assertEquals(self::POST_CODE, $actual->getAddress()->getPostalCode());
    }
}
