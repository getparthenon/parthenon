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

namespace Parthenon\Billing\Subscription;

use Obol\Model\Events\AbstractCharge;
use Obol\Model\PaymentDetails;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class PaymentEventLinker implements PaymentEventLinkerInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function linkPaymentDetailsToSubscription(Payment $payment, PaymentDetails $charge): void
    {
        if (!$charge->getPaymentReference()) {
            return;
        }

        $invoice = $this->provider->invoices()->fetch($charge->getPaymentReference());

        if (!$invoice) {
            return;
        }

        foreach ($invoice->getLines() as $line) {
            if (!$line->hasReferences()) {
                continue;
            }
            try {
                $subscription = $this->subscriptionRepository->getForMainAndChildExternalReference($line->getMainSubscriptionReference(), $line->getChildSubscriptionReference());
                $payment->addSubscription($subscription);
            } catch (NoEntityFoundException $e) {
            }
        }
    }

    public function linkToSubscription(Payment $payment, AbstractCharge $charge): void
    {
        if (!$charge->hasExternalInvoiceId()) {
            return;
        }

        $invoice = $this->provider->invoices()->fetch($charge->getExternalInvoiceId());

        if (!$invoice) {
            return;
        }

        foreach ($invoice->getLines() as $line) {
            if (!$line->hasReferences()) {
                continue;
            }
            try {
                $subscription = $this->subscriptionRepository->getForMainAndChildExternalReference($line->getMainSubscriptionReference(), $line->getChildSubscriptionReference());
                $payment->addSubscription($subscription);
            } catch (NoEntityFoundException $e) {
            }
        }
    }
}
