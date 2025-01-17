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

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Tax\TaxCalculatorInterface;

class ReceiptGenerator implements ReceiptGeneratorInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private TaxCalculatorInterface $taxCalculator,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function generateInvoiceForPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): ReceiptInterface
    {
        $payments = $this->paymentRepository->getPaymentsForCustomerDuring($startDate, $endDate, $customer);

        if (empty($payments)) {
            throw new \Exception('No payments for receipt');
        }

        $total = null;
        $vatTotal = null;
        $subTotalTotal = null;
        $subscriptions = [];
        $lines = [];

        $receipt = $this->entityFactory->getReceipt();
        foreach ($payments as $payment) {
            $subscriptions = array_merge($subscriptions, $payment->getSubscriptions()->toArray());
            $money = $payment->getMoneyAmount();

            $total = $this->addToTotal($total, $money);

            if (0 === $payment->getSubscriptions()->count()) {
                $line = $this->entityFactory->getReceiptLine();
                $line->setTotal($payment->getAmount());
                $line->setCurrency($payment->getCurrency());
                $line->setDescription($payment->getDescription());
                $line->setReceipt($receipt);

                $this->taxCalculator->calculateReceiptLine($customer, $line);

                $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
                $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

                $lines[] = $line;
            }
        }

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $line = new ReceiptLine();
            $line->setTotal($subscription->getAmount());
            $line->setCurrency($subscription->getCurrency());
            $line->setDescription($subscription->getPlanName());
            $line->setReceipt($receipt);

            $this->taxCalculator->calculateReceiptLine($customer, $line);

            $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
            $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

            $lines[] = $line;
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }
        if (!$line instanceof ReceiptLine) {
            throw new \LogicException('There must be at least one line');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments($payments);
        $receipt->setSubscriptions($subscriptions);
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);
        $receipt->setValid(true);
        $receipt->setCurrency($line->getCurrency());
        $receipt->setCreatedAt(new \DateTime());
        $receipt->setPayeeAddress($customer->getBillingAddress());

        return $receipt;
    }

    public function generateReceiptForPayment(Payment $payment): ReceiptInterface
    {
        $receipt = $this->entityFactory->getReceipt();
        $total = $payment->getMoneyAmount();
        $vatTotal = null;
        $subTotalTotal = null;
        $lines = [];
        $customer = $payment->getCustomer();

        /** @var Subscription $subscription */
        foreach ($payment->getSubscriptions() as $subscription) {
            $line = $this->entityFactory->getReceiptLine();
            $line->setTotal($subscription->getAmount());
            $line->setCurrency($subscription->getCurrency());
            $line->setDescription($subscription->getPlanName());
            $line->setReceipt($receipt);

            $this->taxCalculator->calculateReceiptLine($customer, $line);

            $vatTotal = $this->addToTotal($vatTotal, $line->getVatTotalMoney());
            $subTotalTotal = $this->addToTotal($subTotalTotal, $line->getSubTotalMoney());

            $lines[] = $line;
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }

        if (!isset($line)) {
            throw new \LogicException('There must be at least one line');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments([$payment]);
        $receipt->setSubscriptions($payment->getSubscriptions());
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);
        $receipt->setValid(true);
        $receipt->setCurrency($line->getCurrency());
        $receipt->setCreatedAt(new \DateTime());
        $receipt->setVatPercentage($line->getVatPercentage());
        $receipt->setPayeeAddress($customer->getBillingAddress());

        return $receipt;
    }

    private function addToTotal(?Money $total, Money $money): Money
    {
        if (null === $total) {
            $total = $money;
        } else {
            $total = $total->plus($money, RoundingMode::HALF_EVEN);
        }

        return $total;
    }
}
