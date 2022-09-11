<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Stripe;

use Parthenon\Payments\Checkout;
use Parthenon\Subscriptions\Entity\Subscription;
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
