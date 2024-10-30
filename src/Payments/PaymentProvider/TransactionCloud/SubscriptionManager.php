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

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\SubscriptionManagerInterface;
use TransactionCloud\Model\PaymentEntry;
use TransactionCloud\TransactionCloud;

class SubscriptionManager implements SubscriptionManagerInterface
{
    public function __construct(private TransactionCloud $transactionCloud)
    {
    }

    public function cancel(Subscription $subscription)
    {
        $this->transactionCloud->cancelSubscription($subscription->getPaymentId());
    }

    public function change(Subscription $subscription)
    {
        // TODO: Implement change() method.
    }

    public function syncStatus(Subscription $subscription): Subscription
    {
        $transaction = $this->transactionCloud->getTransactionById($subscription->getPaymentId());
        $remoteStatus = $transaction->getTransactionStatus();

        switch ($remoteStatus) {
            case 'SUBSCRIPTION_STATUS_CANCELLED_PENDING':
            case 'SUBSCRIPTION_STATUS_CANCELLED':
                $status = Subscription::STATUS_CANCELLED;
                break;
            default:
                $status = Subscription::STATUS_ACTIVE;
        }

        $subscription->setStatus($status);

        return $subscription;
    }

    public function getInvoiceUrl(Subscription $subscription)
    {
        $transaction = $this->transactionCloud->getTransactionById($subscription->getPaymentId());
        /** @var PaymentEntry $payment */
        $payment = null;
        foreach ($transaction->getEntries() as $entry) {
            if (null === $payment || $payment->getCreateDate() < $entry->getCreateDate()) {
                $payment = $entry;
            }
        }

        if (null === $payment) {
            return null;
        }

        return $this->transactionCloud->getInvoiceUrlForPayment($entry);
    }

    public function getBillingPortal(Subscription $subscription): string
    {
        return $this->transactionCloud->getUrlToManageTransactions($subscription->getCustomerId());
    }
}
