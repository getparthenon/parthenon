<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
