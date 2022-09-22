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
use Parthenon\Payments\Checkout;
use Parthenon\Payments\CheckoutInterface;
use Parthenon\Payments\CheckoutManagerInterface;
use Parthenon\Payments\Exception\NoCheckoutFoundException;
use Parthenon\Payments\Entity\Subscription;
use Stripe\StripeClient;

class CheckoutManager implements CheckoutManagerInterface
{
    public function __construct(private Config $config, private StripeClient $stripeClient)
    {
    }

    public function createCheckoutForSubscription(Subscription $subscription, array $options = [], int $seats = 1): CheckoutInterface
    {
        $lineItems = [];

        $lineItems[] = [
            'description' => $subscription->getPlanName(),
            'price' => $subscription->getPriceId(),
            'quantity' => $seats,
        ];

        if (!isset($options['payment_method_types'])) {
            $options['payment_method_types'] = ['card'];
        }

        $options['mode'] = Subscription::PAYMENT_SCHEDULE_LIFETIME === $subscription->getPaymentSchedule() ? 'payment' : 'subscription';
        $options['line_items'] = $lineItems;
        $options['success_url'] = $this->config->getSuccessUrl();
        $options['cancel_url'] = $this->config->getCancelUrl();
        $options['allow_promotion_codes'] = true;

        try {
            $session = $this->stripeClient->checkout->sessions->create($options);
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }

        $subscription->setCheckoutId($session->id);

        return new Checkout($session->id);
    }

    public function handleSuccess(Subscription $subscription): void
    {
        try {
            $stripeSession = $this->stripeClient->checkout->sessions->retrieve($subscription->getCheckoutId());
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$stripeSession) { /* @phpstan-ignore-line */
            throw new NoCheckoutFoundException();
        }

        if ('paid' !== $stripeSession->payment_status) {
            return;
        }

        $subscription->setStatus(Subscription::STATUS_ACTIVE);
        $subscription->setCustomerId($stripeSession->customer->id);
        $subscription->increaseValidUntil();
        if (Subscription::PAYMENT_SCHEDULE_LIFETIME !== $subscription->getPaymentSchedule()) {
            $subscription->setPaymentId($stripeSession->subscription);
        }
    }
}
