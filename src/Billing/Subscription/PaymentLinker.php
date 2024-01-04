<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
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
use Parthenon\Common\LoggerAwareTrait;

class PaymentLinker implements PaymentLinkerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function linkPaymentDetailsToSubscription(Payment $payment, PaymentDetails $charge): void
    {
        if (!$charge->getInvoiceReference()) {
            return;
        }

        $invoice = $this->provider->invoices()->fetch($charge->getInvoiceReference());

        if (!$invoice) {
            $this->getLogger()->warning('No invoice found to link payment details', ['invoice_reference' => $charge->getInvoiceReference()]);

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
            $this->getLogger()->warning('Charge does not have an external id');

            return;
        }

        $invoice = $this->provider->invoices()->fetch($charge->getExternalInvoiceId());

        if (!$invoice) {
            $this->getLogger()->info('No invoice found to link to subscription');

            return;
        }

        foreach ($invoice->getLines() as $line) {
            if (!$line->hasReferences()) {
                $this->getLogger()->info("Don't have references");
                continue;
            }
            try {
                $subscription = $this->subscriptionRepository->getForMainAndChildExternalReference($line->getMainSubscriptionReference(), $line->getChildSubscriptionReference());
                $payment->addSubscription($subscription);
            } catch (NoEntityFoundException $e) {
                $this->getLogger()->warning('Unable to find subscription for invoice', ['main_subscription_reference' => $line->getMainSubscriptionReference(), 'child_subscription_reference' => $line->getChildSubscriptionReference()]);
            }
        }
    }
}
