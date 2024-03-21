<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Payments\Stripe;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Payments\Checkout;
use Parthenon\Payments\CheckoutInterface;
use Parthenon\Payments\CheckoutManagerInterface;
use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Exception\NoCheckoutFoundException;
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
