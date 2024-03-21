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

use Parthenon\Payments\Checkout;
use Parthenon\Payments\Entity\Subscription;
use PHPUnit\Framework\TestCase;
use Stripe\Checkout\Session;
use Stripe\Service\Checkout\CheckoutServiceFactory;
use Stripe\Service\Checkout\SessionService;
use Stripe\StripeClient;

class CheckoutManagerTest extends TestCase
{
    public function testCreatesSubscriptionSession()
    {
        $subscription = $this->createMock(Subscription::class);
        $config = $this->createMock(Config::class);
        $stripeClient = $this->createMock(StripeClient::class);
        $checkoutServiceFactory = $this->createMock(CheckoutServiceFactory::class);
        $sessionService = $this->createMock(SessionService::class);

        $config->method('getCancelUrl')->willReturn('cancel_url');
        $config->method('getSuccessUrl')->willReturn('success_url');

        $session = new Session('checkout_id');
        $stripeClient->checkout = $checkoutServiceFactory;
        $checkoutServiceFactory->sessions = $sessionService;

        $sessionService->expects($this->once())->method('create')->with([
            'mode' => 'subscription',
            'line_items' => [
                [
                    'description' => 'plan_name',
                    'price' => 'price_id',
                    'quantity' => 1,
                ],
            ],
            'success_url' => 'success_url',
            'cancel_url' => 'cancel_url',
            'payment_method_types' => ['card'],
            'allow_promotion_codes' => true,
        ])->willReturn($session);

        $subscription->method('getPriceId')->willReturn('price_id');
        $subscription->method('getPlanName')->willReturn('plan_name');
        $subscription->expects($this->once())->method('setCheckoutId')->with('checkout_id');

        $checkoutManager = new CheckoutManager($config, $stripeClient);

        $session = $checkoutManager->createCheckoutForSubscription($subscription);

        $this->assertInstanceOf(Checkout::class, $session);
        $this->assertEquals('checkout_id', $session->getId());
    }
}
