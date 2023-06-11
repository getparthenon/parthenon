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

namespace Parthenon\Billing\Receipt;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Tax\CountryRules;
use Parthenon\Billing\Tax\TaxCalculator;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;

class ReceiptGeneratorTest extends TestCase
{
    public function testGenerateReceiptFromPayment()
    {
        $amount = Money::ofMinor(10000, 'USD');
        $subscriptionOne = $this->createMock(Subscription::class);
        $subscriptionOne->method('getMoneyAmount')->willReturn($amount);
        $subscriptionOne->method('getAmount')->willReturn(10000);
        $subscriptionOne->method('getCurrency')->willReturn('USD');
        $subscriptionOne->method('getPlanName')->willReturn('Test One');

        $subscriptions = new ArrayCollection([$subscriptionOne]);

        $address = new Address();
        $address->setCountry('GB');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $payment = $this->createMock(Payment::class);
        $payment->method('getSubscriptions')->willReturn($subscriptions);
        $payment->method('getCustomer')->willReturn($customer);
        $payment->method('getMoneyAmount')->willReturn($amount);

        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);

        $subject = new ReceiptGenerator($paymentRepository, new TaxCalculator(new CountryRules()));
        $receipt = $subject->generateReceiptForPayment($payment);

        $this->assertEquals(10000, $receipt->getTotal());
        $this->assertEquals(8333, $receipt->getSubTotal());
        $this->assertEquals(1667, $receipt->getVatTotal());
    }

    public function testGenerateReceiptFromPaymentMultipleSubscriptions()
    {
        $amountOne = Money::ofMinor(10000, 'USD');
        $subscriptionOne = $this->createMock(Subscription::class);
        $subscriptionOne->method('getMoneyAmount')->willReturn($amountOne);
        $subscriptionOne->method('getAmount')->willReturn(10000);
        $subscriptionOne->method('getCurrency')->willReturn('USD');
        $subscriptionOne->method('getPlanName')->willReturn('Test One');

        $amountTwo = Money::ofMinor(12345, 'USD');
        $subscriptionTwo = $this->createMock(Subscription::class);
        $subscriptionTwo->method('getMoneyAmount')->willReturn($amountTwo);
        $subscriptionTwo->method('getAmount')->willReturn(12345);
        $subscriptionTwo->method('getCurrency')->willReturn('USD');
        $subscriptionTwo->method('getPlanName')->willReturn('Test One');

        $subscriptions = new ArrayCollection([$subscriptionOne, $subscriptionTwo]);

        $address = new Address();
        $address->setCountry('GB');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getBillingAddress')->willReturn($address);

        $payment = $this->createMock(Payment::class);
        $payment->method('getSubscriptions')->willReturn($subscriptions);
        $payment->method('getCustomer')->willReturn($customer);
        $payment->method('getMoneyAmount')->willReturn(Money::ofMinor(22345, 'USD'));

        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);

        $subject = new ReceiptGenerator($paymentRepository, new TaxCalculator(new CountryRules()));
        $receipt = $subject->generateReceiptForPayment($payment);

        $this->assertEquals(22345, $receipt->getTotal());
        $this->assertEquals(18621, $receipt->getSubTotal());
        $this->assertEquals(3724, $receipt->getVatTotal());
    }
}
