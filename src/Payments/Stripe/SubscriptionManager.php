<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Stripe;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\SubscriptionManagerInterface;
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
            $this->getLogger()->error('Subscription is itemless', ['subscription_payment_id' => $subscription->getPaymentId()]);
            throw new GeneralException('Subscription is itemless');
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
