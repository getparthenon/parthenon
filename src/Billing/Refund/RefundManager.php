<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Billing\Refund;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Obol\Model\Refund\IssueRefund;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Enum\RefundStatus;
use Parthenon\Billing\Event\RefundCreated;
use Parthenon\Billing\Exception\RefundLimitExceededException;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RefundManager implements RefundManagerInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private PaymentRepositoryInterface $paymentRepository,
        private RefundRepositoryInterface $refundRepository,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function issueRefundForPayment(Payment $payment, Money $amount, ?BillingAdminInterface $billingAdmin = null, ?string $reason = null): Refund
    {
        $totalRefunded = $this->refundRepository->getTotalRefundedForPayment($payment);

        if ($totalRefunded->isGreaterThanOrEqualTo($payment->getMoneyAmount())) {
            throw new RefundLimitExceededException('Payment has already been fully refunded');
        }

        $refundable = $payment->getMoneyAmount()->minus($totalRefunded);

        if ($amount->isGreaterThan($refundable)) {
            throw new RefundLimitExceededException('The refund amount is greater than the refundable amount for payment');
        }

        return $this->handleRefund($amount, $payment, $totalRefunded, $billingAdmin, $reason);
    }

    public function issueFullRefundForSubscription(Subscription $subscription, ?BillingAdminInterface $billingAdmin = null): Refund
    {
        $payment = $this->paymentRepository->getLastPaymentForSubscription($subscription);
        $totalRefunded = $this->refundRepository->getTotalRefundedForPayment($payment);

        $amount = $subscription->getMoneyAmount();
        $refundable = $payment->getMoneyAmount()->minus($totalRefunded);

        if ($amount->isGreaterThan($refundable)) {
            throw new RefundLimitExceededException('The refund amount is greater than the refundable amount for payment');
        }

        return $this->handleRefund($amount, $payment, $totalRefunded, $billingAdmin);
    }

    public function issueProrateRefundForSubscription(Subscription $subscription, ?BillingAdminInterface $billingAdmin, \DateTimeInterface $start, \DateTimeInterface $end): Refund
    {
        if ('month' === $subscription->getPaymentSchedule()) {
            $days = date('t');
        } elseif ('year' === $subscription->getPaymentSchedule()) {
            $days = 365;
        } else {
            $days = 7;
        }

        $interval = $start->diff($end);
        if (!is_int($interval->days)) {
            throw new \Exception('Invalid diff');
        }

        $payment = $this->paymentRepository->getLastPaymentForSubscription($subscription);
        $totalRefunded = $this->refundRepository->getTotalRefundedForPayment($payment);
        $refundable = $payment->getMoneyAmount()->minus($totalRefunded);

        $perDay = $subscription->getMoneyAmount()->dividedBy($days, RoundingMode::HALF_UP);
        $totalAmount = $perDay->multipliedBy(abs($interval->days), RoundingMode::HALF_UP)->multipliedBy($subscription->getSeats(), RoundingMode::HALF_UP);

        if ($totalAmount->isGreaterThan($refundable)) {
            throw new RefundLimitExceededException('The refund amount is greater than the refundable amount for payment');
        }

        return $this->handleRefund($totalAmount, $payment, $totalRefunded, $billingAdmin);
    }

    protected function createEntityRecord(\Obol\Model\Refund $refund, ?BillingAdminInterface $billingAdmin, Payment $payment, ?string $reason = null): Refund
    {
        $money = Money::ofMinor($refund->getAmount(), Currency::of(strtoupper($refund->getCurrency())));
        if ($payment->getMoneyAmount()->isEqualTo($money)) {
            $payment->setStatus(PaymentStatus::FULLY_REFUNDED);
        } else {
            $payment->setStatus(PaymentStatus::PARTIALLY_REFUNDED);
        }
        $this->paymentRepository->save($payment);
        $refundEn = new Refund();
        $refundEn->setAmount($refund->getAmount());
        $refundEn->setCurrency(strtoupper($refund->getCurrency()));
        $refundEn->setExternalReference($refund->getId());
        $refundEn->setStatus(RefundStatus::ISSUED);
        $refundEn->setBillingAdmin($billingAdmin);
        $refundEn->setPayment($payment);
        $refundEn->setCustomer($payment->getCustomer());
        $refundEn->setCreatedAt(new \DateTime());
        $refundEn->setReason($reason);

        $this->refundRepository->save($refundEn);
        $this->dispatcher->dispatch(new RefundCreated($refundEn), RefundCreated::NAME);

        return $refundEn;
    }

    /**
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Obol\Exception\UnsupportedFunctionalityException
     */
    protected function handleRefund(Money $amount, Payment $payment, Money $totalRefunded, ?BillingAdminInterface $billingAdmin, ?string $reason = null): Refund
    {
        $issueRefund = new IssueRefund();
        $issueRefund->setAmount($amount);
        $issueRefund->setPaymentExternalReference($payment->getPaymentReference());

        $refund = $this->provider->refunds()->issueRefund($issueRefund);

        $totalRefunded = $totalRefunded->plus($amount);

        if ($totalRefunded->isEqualTo($payment->getAmount())) {
            $payment->setStatus(PaymentStatus::FULLY_REFUNDED);
        } else {
            $payment->setStatus(PaymentStatus::PARTIALLY_REFUNDED);
        }

        return $this->createEntityRecord($refund, $billingAdmin, $payment, $reason);
    }
}
