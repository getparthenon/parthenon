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

namespace Parthenon\Billing\Receipt;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Tax\TaxCalculatorInterface;

class ReceiptGenerator implements ReceiptGeneratorInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private TaxCalculatorInterface $taxCalculator,
    ) {
    }

    public function generateInvoiceForPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): Receipt
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

        $receipt = new Receipt();
        foreach ($payments as $payment) {
            $subscriptions = array_merge($subscriptions, $payment->getSubscriptions()->toArray());
            $money = $payment->getMoneyAmount();

            $total = $this->addToTotal($total, $money);

            if (0 === $payment->getSubscriptions()->count()) {
                $line = new ReceiptLine();
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

    public function generateReceiptForPayment(Payment $payment): Receipt
    {
        $receipt = new Receipt();
        $total = $payment->getMoneyAmount();
        $vatTotal = null;
        $subTotalTotal = null;
        $lines = [];
        $customer = $payment->getCustomer();

        /** @var Subscription $subscription */
        foreach ($payment->getSubscriptions() as $subscription) {
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
