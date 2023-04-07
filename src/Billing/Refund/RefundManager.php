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

use Obol\Model\Refund\IssueRefund;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;

class RefundManager implements RefundManagerInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private PaymentRepositoryInterface $paymentRepository,
        private RefundRepositoryInterface $refundRepository,
    ) {
    }

    public function issueFullRefundForSubscription(Subscription $subscription, BillingAdminInterface $billingAdmin): void
    {
        $payment = $this->paymentRepository->getLastPaymentForSubscription($subscription);

        $issueRefund = new IssueRefund();
        $issueRefund->setAmount($subscription->getMoneyAmount());
        $issueRefund->setPaymentExternalReference($payment->getPaymentReference());

        $refund = $this->provider->refunds()->issueRefund($issueRefund);

        $refundEn = new Refund();
        $refundEn->setAmount($refund->getAmount());
        $refundEn->setCurrency($refund->getCurrency());
        $refundEn->setExternalReference($refund->getId());
        $refundEn->setStatus('refunded');
        $refundEn->setBillingAdmin($billingAdmin);
        $refundEn->setPayment($payment);
        $refundEn->setCustomer($subscription->getCustomer());
        $refundEn->setCreatedAt(new \DateTime());

        $this->refundRepository->save($refundEn);
    }
}
