<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Billing\Refund;

use Brick\Money\Currency;
use Brick\Money\Money;
use Obol\Model\Refund;
use Obol\Provider\ProviderInterface;
use Obol\RefundServiceInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund as RefundEntity;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Factory\EntityFactory;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RefundManagerTest extends TestCase
{
    public function testProrate()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $billingAdmin = $this->createMock(BillingAdminInterface::class);

        $price = Money::of(7, Currency::of('USD'));
        $subscription = $this->createMock(Subscription::class);
        $subscription->method('getPaymentSchedule')->willReturn('week');
        $subscription->method('getMoneyAmount')->willReturn($price);
        $subscription->method('getCustomer')->willReturn($customer);
        $subscription->method('getSeats')->willReturn(1);

        $provider = $this->createMock(ProviderInterface::class);
        $refundService = $this->createMock(RefundServiceInterface::class);
        $provider->method('refunds')->willReturn($refundService);

        $customer = $this->createMock(CustomerInterface::class);
        $payment = $this->createMock(Payment::class);
        $payment->method('getCustomer')->willReturn($customer);
        $payment->method('getPaymentReference')->willReturn('payment_id');
        $payment->method('getMoneyAmount')->willReturn(Money::of(1000, Currency::of('USD')));

        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $paymentRepository->method('getLastPaymentForSubscription')->with($subscription)->willReturn($payment);

        $refundRepository = $this->createMock(RefundRepositoryInterface::class);
        $refundRepository->method('save')->with($this->isInstanceOf(RefundEntity::class));
        $refundRepository->method('getTotalRefundedForPayment')->with($payment)->willReturn(Money::of(0, Currency::of('USD')));

        $refundCreation = new Refund();
        $refundCreation->setAmount(300);
        $refundCreation->setCurrency('USD');
        $refundCreation->setId('rf_dfjdsf');

        $refundService->expects($this->once())->method('issueRefund')->with($this->callback(function (Refund\IssueRefund $refund) {
            if (!$refund->getAmount()->isEqualTo(3)) {
                return false;
            }

            return true;
        }))->willReturn($refundCreation);

        $start = new \DateTime('now');
        $end = new \DateTime('+3 days');

        $subject = new RefundManager($provider, $paymentRepository, $refundRepository, $dispatcher, new EntityFactory());
        $subject->issueProrateRefundForSubscription($subscription, $billingAdmin, $start, $end);
    }

    public function testProrateYear()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $billingAdmin = $this->createMock(BillingAdminInterface::class);

        $price = Money::of(365, Currency::of('USD'));
        $subscription = $this->createMock(Subscription::class);
        $subscription->method('getPaymentSchedule')->willReturn('year');
        $subscription->method('getMoneyAmount')->willReturn($price);
        $subscription->method('getCustomer')->willReturn($customer);
        $subscription->method('getSeats')->willReturn(1);

        $provider = $this->createMock(ProviderInterface::class);
        $refundService = $this->createMock(RefundServiceInterface::class);
        $provider->method('refunds')->willReturn($refundService);
        $customer = $this->createMock(CustomerInterface::class);
        $payment = $this->createMock(Payment::class);
        $payment->method('getCustomer')->willReturn($customer);
        $payment->method('getPaymentReference')->willReturn('payment_id');
        $payment->method('getMoneyAmount')->willReturn(Money::of(1000, Currency::of('USD')));

        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $paymentRepository->method('getLastPaymentForSubscription')->with($subscription)->willReturn($payment);

        $refundRepository = $this->createMock(RefundRepositoryInterface::class);
        $refundRepository->method('save')->with($this->isInstanceOf(RefundEntity::class));
        $refundRepository->method('getTotalRefundedForPayment')->with($payment)->willReturn(Money::of(0, Currency::of('USD')));

        $refundCreation = new Refund();
        $refundCreation->setAmount(300);
        $refundCreation->setCurrency('USD');
        $refundCreation->setId('rf_dfjdsf');

        $refundService->expects($this->once())->method('issueRefund')->with($this->callback(function (Refund\IssueRefund $refund) {
            if (!$refund->getAmount()->isEqualTo(3)) {
                return false;
            }

            return true;
        }))->willReturn($refundCreation);

        $start = new \DateTime('now');
        $end = new \DateTime('+3 days');

        $subject = new RefundManager($provider, $paymentRepository, $refundRepository, $dispatcher, new EntityFactory());
        $subject->issueProrateRefundForSubscription($subscription, $billingAdmin, $start, $end);
    }
}
