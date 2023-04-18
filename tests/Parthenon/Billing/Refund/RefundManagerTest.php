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
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RefundManagerTest extends TestCase
{
    public function testProrate()
    {
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

        $payment = $this->createMock(Payment::class);
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

        $subject = new RefundManager($provider, $paymentRepository, $refundRepository);
        $subject->issueProrateRefundForSubscription($subscription, $billingAdmin, $start, $end);
    }

    public function testProrateYear()
    {
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

        $payment = $this->createMock(Payment::class);
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

        $subject = new RefundManager($provider, $paymentRepository, $refundRepository);
        $subject->issueProrateRefundForSubscription($subscription, $billingAdmin, $start, $end);
    }
}
