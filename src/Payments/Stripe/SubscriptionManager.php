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

namespace Parthenon\Payments\Stripe;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\SubscriptionManagerInterface;
use Stripe\StripeClient;

final class SubscriptionManager implements SubscriptionManagerInterface
{
    use LoggerAwareTrait;

    public function __construct(private StripeClient $client, private Config $config)
    {
    }

    public function cancel(Subscription $subscription)
    {
        try {
            if (!empty($subscription->getPaymentId())) {
                $stripeSubscription = $this->client->subscriptions->retrieve($subscription->getPaymentId());
                if ('canceled' !== $stripeSubscription->status) {
                    $stripeSubscription->cancel();
                }
            }
            $subscription->setStatus(Subscription::STATUS_CANCELLED);
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while trying to cancel a subscription.', ['exception_message' => $e->getMessage(), 'subscription_payment_id' => $subscription->getPaymentId()]);
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function change(Subscription $subscription)
    {
        $stripeSubscription = $this->client->subscriptions->retrieve($subscription->getPaymentId());

        if (!isset($stripeSubscription->items->data[0]->id)) {
            $this->getLogger()->error('EmbeddedSubscription is itemless', ['subscription_payment_id' => $subscription->getPaymentId()]);
            throw new GeneralException('EmbeddedSubscription is itemless');
        }
        try {
            $this->client->subscriptions->update($subscription->getPaymentId(), [
                'cancel_at_period_end' => false,
                'proration_behavior' => 'create_prorations',
                'items' => [
                    [
                        'id' => $stripeSubscription->items->data[0]->id,
                        'price' => $subscription->getPriceId(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while trying to change a subscription.', ['exception_message' => $e->getMessage(), 'subscription_payment_id' => $subscription->getPaymentId()]);
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getInvoiceUrl(Subscription $subscription)
    {
        try {
            $stripeSubscription = $this->client->subscriptions->retrieve($subscription->getPaymentId());

            return $this->client->invoices->retrieve($stripeSubscription->latest_invoice)->invoice_pdf;
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while trying to get an invocie URL', ['exception_message' => $e->getMessage(), 'subscription_payment_id' => $subscription->getPaymentId()]);

            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function syncStatus(Subscription $subscription): Subscription
    {
        try {
            $stripeSubscription = $this->client->subscriptions->retrieve($subscription->getPaymentId());

            $validUntil = new \DateTime('now', new \DateTimeZone('UTC'));
            $validUntil->setTimestamp($stripeSubscription->current_period_end);

            $subscription->setValidUntil($validUntil);

            switch ($stripeSubscription->status) {
                case 'incomplete':
                case 'past_due':
                    $subscription->setStatus(Subscription::STATUS_OVERDUE);
                    break;
                case 'incomplete_expired':
                case 'unpaid':
                case 'canceled':
                    $subscription->setStatus(Subscription::STATUS_CANCELLED);
                    break;
                case 'active':
                    $subscription->setStatus(Subscription::STATUS_ACTIVE);
                    break;
                case 'trialing':
                    $subscription->setStatus(Subscription::STATUS_TRIAL);
                    break;
            }

            return $subscription;
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while trying to sync status', ['exception_message' => $e->getMessage(), 'subscription_payment_id' => $subscription->getPaymentId()]);

            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getBillingPortal(Subscription $subscription): string
    {
        try {
            $stripeSubscription = $this->client->subscriptions->retrieve($subscription->getPaymentId());
            $session = $this->client->billingPortal->sessions->create(['customer' => $stripeSubscription->customer, 'return_url' => $this->config->getReturnUrl()]);

            return (string) $session->url;
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while trying get the billing portal url', ['exception_message' => $e->getMessage(), 'subscription_payment_id' => $subscription->getPaymentId()]);

            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
