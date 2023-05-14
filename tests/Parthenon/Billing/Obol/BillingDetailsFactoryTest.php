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

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;

class BillingDetailsFactoryTest extends TestCase
{
    public const EMAIL = 'iain.cambridge@example.org';
    public const CUSTOMER_REFERENCE = 'a-reference';
    public const STREET_LINE_ONE = '1 Example Way';
    public const EXAMPLE_TOWN = 'Example Town';
    public const REGION = 'Example';
    public const COUNTRY_CODE = 'US';
    public const POST_CODE = 'GE384';
    public const PAYMENT_REFERENCE = 'payment-reference';

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

        $paymentDetails = new PaymentCard();
        $paymentDetails->setStoredPaymentReference(self::PAYMENT_REFERENCE);

        $subject = new BillingDetailsFactory();
        $actual = $subject->createFromCustomerAndPaymentDetails($customer, $paymentDetails);
        $this->assertEquals(self::PAYMENT_REFERENCE, $actual->getStoredPaymentReference());
        $this->assertEquals(self::CUSTOMER_REFERENCE, $actual->getCustomerReference());
        $this->assertEquals(self::STREET_LINE_ONE, $actual->getAddress()->getStreetLineOne());
    }
}
