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

    public function testConvertCustomer()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $address = $this->createMock(Address::class);

        $customer->method('getBillingEmail')->willReturn(self::EMAIL);
        $customer->method('getExternalCustomerReference')->willReturn(self::CUSTOMER_REFERENCE);
        $customer->method('getBillingAddress')->willReturn($address);

        $address->method('getStreetLineOne')->willReturn(self::STREET_LINE_ONE);
        $address->method('getCity')->willReturn('Example Town');
        $address->method('getRegion')->willReturn('Example');
        $address->method('getCountry')->willReturn('US');
        $address->method('getPostcode')->willReturn('GE384');

        $subject = new CustomerConverter();
        $actual = $subject->convertToBillingDetails($customer);
        $this->assertInstanceOf(BillingDetails::class, $actual);

        $this->assertEquals(self::EMAIL, $actual->getEmail());
        $this->assertEquals(self::CUSTOMER_REFERENCE, $actual->getCustomerReference());
        $this->assertEquals(self::STREET_LINE_ONE, $actual->getAddress()->getStreetLineOne());
    }
}
