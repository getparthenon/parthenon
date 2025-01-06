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

namespace Parthenon\Billing\Receipt;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Factory\EntityFactory;
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

        $subject = new ReceiptGenerator($paymentRepository, new TaxCalculator(new CountryRules()), new EntityFactory());
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

        $subject = new ReceiptGenerator($paymentRepository, new TaxCalculator(new CountryRules()), new EntityFactory());
        $receipt = $subject->generateReceiptForPayment($payment);

        $this->assertEquals(22345, $receipt->getTotal());
        $this->assertEquals(18621, $receipt->getSubTotal());
        $this->assertEquals(3724, $receipt->getVatTotal());
    }
}
