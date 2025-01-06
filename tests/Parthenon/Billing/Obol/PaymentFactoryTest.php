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
