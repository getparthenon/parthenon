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

use Brick\Money\Money;
use Obol\Model\PaymentDetails;
use Obol\Model\SubscriptionCreationResponse;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Factory\EntityFactory;
use PHPUnit\Framework\TestCase;

class PaymentFactoryTest extends TestCase
{
    public function testFromSubscriptionConfirm()
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customerProvider = $this->createMock(CustomerProviderInterface::class);
        $customerProvider->method('getCurrentCustomer')->willReturn($customer);

        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('getName')->willReturn('stripe');

        $amount = Money::of(1000, 'USD');

        $paymentDetails = new PaymentDetails();
        $paymentDetails->setAmount($amount);
        $paymentDetails->setPaymentReference('payment-reference');
        $paymentDetails->setStoredPaymentReference('stored-payment-reference');
        $paymentDetails->setCustomerReference('customer-reference');

        $subscriptionCreation = new SubscriptionCreationResponse();
        $subscriptionCreation->setSubscriptionId('subscription-id');
        $subscriptionCreation->setPaymentDetails($paymentDetails);

        $subject = new PaymentFactory($customerProvider, $provider, new EntityFactory());

        $actual = $subject->fromSubscriptionCreation($paymentDetails);
        $this->assertTrue($amount->isEqualTo($actual->getMoneyAmount()));
        $this->assertEquals('payment-reference', $actual->getPaymentReference());
        $this->assertSame($customer, $actual->getCustomer());
        $this->assertTrue($actual->isCompleted());
        $this->assertFalse($actual->isRefunded());
        $this->assertFalse($actual->isChargedBack());
        $this->assertEquals('stripe', $actual->getProvider());
    }
}
