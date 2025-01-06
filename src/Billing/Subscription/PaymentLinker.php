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
