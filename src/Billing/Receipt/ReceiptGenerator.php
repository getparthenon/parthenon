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
                $vat = $this->taxCalculator->calculateVatAmountForCustomer($customer, $payment->getMoneyAmount());
                $subTotal = $this->taxCalculator->calculateSubTotalForCustomer($customer, $money);
                $vatTotal = $this->addToTotal($vatTotal, $vat);
                $subTotalTotal = $this->addToTotal($subTotalTotal, $subTotal);

                $line = new ReceiptLine();
                $line->setTotal($payment->getAmount());
                $line->setCurrency($payment->getCurrency());
                $line->setDescription($payment->getDescription());
                $line->setReceipt($receipt);
                $line->setVatTotal($vat->getMinorAmount()->toInt());
                $line->setSubTotal($subTotal->getMinorAmount()->toInt());

                $lines[] = $line;
            }
        }

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $money = $subscription->getMoneyAmount();

            $vat = $this->taxCalculator->calculateVatAmountForCustomer($customer, $money);
            $subTotal = $money->minus($vat, RoundingMode::HALF_DOWN);

            $vatTotal = $this->addToTotal($vatTotal, $vat);
            $subTotalTotal = $this->addToTotal($subTotalTotal, $subTotal);

            $line = new ReceiptLine();
            $line->setTotal($subscription->getAmount());
            $line->setCurrency($subscription->getCurrency());
            $line->setDescription($subscription->getPlanName());
            $line->setReceipt($receipt);
            $line->setVatTotal($vat->getMinorAmount()->toInt());
            $line->setSubTotal($subTotal->getMinorAmount()->toInt());

            $lines[] = $line;
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments($payments);
        $receipt->setSubscriptions($subscriptions);
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);

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
            $money = $subscription->getMoneyAmount();

            $vat = $this->taxCalculator->calculateVatAmountForCustomer($customer, $money);
            $subTotal = $this->taxCalculator->calculateSubTotalForCustomer($customer, $money);

            $vatTotal = $this->addToTotal($vatTotal, $vat);
            $subTotalTotal = $this->addToTotal($subTotalTotal, $subTotal);

            $line = new ReceiptLine();
            $line->setTotal($subscription->getAmount());
            $line->setCurrency($subscription->getCurrency());
            $line->setDescription($subscription->getPlanName());
            $line->setReceipt($receipt);
            $line->setVatTotal($vat->getMinorAmount()->toInt());
            $line->setSubTotal($subTotal->getMinorAmount()->toInt());

            $lines[] = $line;
        }

        if (!$total instanceof Money) {
            throw new \LogicException('Total must be money if payments exist');
        }

        $receipt->setCustomer($customer);
        $receipt->setPayments([$payment]);
        $receipt->setSubscriptions($payment->getSubscriptions());
        $receipt->setTotal($total->getMinorAmount()->toInt());
        $receipt->setSubTotal($subTotalTotal->getMinorAmount()->toInt());
        $receipt->setVatTotal($vatTotal->getMinorAmount()->toInt());
        $receipt->setLines($lines);

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
