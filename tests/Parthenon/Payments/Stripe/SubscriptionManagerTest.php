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

use Parthenon\Payments\Entity\Subscription;
use PHPUnit\Framework\TestCase;
use Stripe\Collection;
use Stripe\Invoice;
use Stripe\LineItem;
use Stripe\Service\InvoiceService;
use Stripe\Service\SubscriptionService;
use Stripe\StripeClient;
use Stripe\Subscription as StripeSubscription;

class SubscriptionManagerTest extends TestCase
{
    public function testCancelsSubscription()
    {
        $id = 'asdsa';

        $stripeClient = $this->createMock(StripeClient::class);
        $stripeConfig = $this->createMock(Config::class);
        $subscriptionService = $this->createMock(SubscriptionService::class);
        $subscription = $this->createMock(Subscription::class);
        $stripeSubscription = $this->createMock(StripeSubscription::class);

        $subscription->method('getPaymentId')->willReturn($id);
        $subscription->expects($this->once())->method('setStatus')->with(Subscription::STATUS_CANCELLED);

        $stripeClient->subscriptions = $subscriptionService;

        $subscriptionService->method('retrieve')->with($id)->willReturn($stripeSubscription);

        $stripeSubscription->expects($this->once())->method('cancel');

        $subscriptionManager = new SubscriptionManager($stripeClient, $stripeConfig);
        $subscriptionManager->cancel($subscription);
    }

    public function testCancelsSubscriptionNoPaymentId()
    {
        $id = '';

        $stripeClient = $this->createMock(StripeClient::class);
        $stripeConfig = $this->createMock(Config::class);
        $subscriptionService = $this->createMock(SubscriptionService::class);
        $subscription = $this->createMock(Subscription::class);
        $stripeSubscription = $this->createMock(StripeSubscription::class);

        $subscription->method('getPaymentId')->willReturn($id);
        $subscription->expects($this->once())->method('setStatus')->with(Subscription::STATUS_CANCELLED);

        $stripeClient->subscriptions = $subscriptionService;

        $subscriptionService->expects($this->never())->method('retrieve')->with($id)->willReturn($stripeSubscription);

        $stripeSubscription->expects($this->never())->method('cancel');

        $subscriptionManager = new SubscriptionManager($stripeClient, $stripeConfig);
        $subscriptionManager->cancel($subscription);
    }

    public function testCancelsSubscriptionNullPaymentId()
    {
        $id = null;

        $stripeClient = $this->createMock(StripeClient::class);
        $stripeConfig = $this->createMock(Config::class);
        $subscriptionService = $this->createMock(SubscriptionService::class);
        $subscription = $this->createMock(Subscription::class);
        $stripeSubscription = $this->createMock(StripeSubscription::class);

        $subscription->method('getPaymentId')->willReturn($id);
        $subscription->expects($this->once())->method('setStatus')->with(Subscription::STATUS_CANCELLED);

        $stripeClient->subscriptions = $subscriptionService;

        $subscriptionService->expects($this->never())->method('retrieve')->with($id)->willReturn($stripeSubscription);

        $stripeSubscription->expects($this->never())->method('cancel');

        $subscriptionManager = new SubscriptionManager($stripeClient, $stripeConfig);
        $subscriptionManager->cancel($subscription);
    }

    public function testGetUrls()
    {
        $id = 'asdsa';
        $url = 'invoice_url';
        $invoiceId = 'invoice_id';

        $stripeSubscription = new Subscription();
        $stripeSubscription->latest_invoice = $invoiceId;

        $stripeInvoice = new Invoice();
        $stripeInvoice->invoice_pdf = $url;

        $stripeClient = $this->createMock(StripeClient::class);
        $stripeConfig = $this->createMock(Config::class);
        $subscriptionService = $this->createMock(SubscriptionService::class);
        $invoiceService = $this->createMock(InvoiceService::class);
        $subscription = $this->createMock(Subscription::class);

        $subscription->method('getPaymentId')->willReturn($id);

        $stripeClient->subscriptions = $subscriptionService;
        $stripeClient->invoices = $invoiceService;

        $subscriptionService->method('retrieve')->with($id)->willReturn($stripeSubscription);
        $invoiceService->method('retrieve')->with($invoiceId)->willReturn($stripeInvoice);

        $subscriptionManager = new SubscriptionManager($stripeClient, $stripeConfig);
        $this->assertEquals($url, $subscriptionManager->getInvoiceUrl($subscription));
    }

    public function testChangesSubscription()
    {
        $id = 'asdsa';
        $priceId = 'price_id';
        $lineId = 'line_id';

        $lineItem = new LineItem($lineId);

        $stripeSubscription = new Subscription();
        $stripeSubscription->items = new Collection();
        $stripeSubscription->items->data = [$lineItem];

        $stripeClient = $this->createMock(StripeClient::class);
        $stripeConfig = $this->createMock(Config::class);
        $subscriptionService = $this->createMock(SubscriptionService::class);
        $subscription = $this->createMock(Subscription::class);

        $subscription->method('getPaymentId')->willReturn($id);
        $subscription->method('getPriceId')->willReturn($priceId);

        $stripeClient->subscriptions = $subscriptionService;

        $subscriptionService->method('retrieve')->with($id)->willReturn($stripeSubscription);
        $subscriptionService->expects($this->once())->method('update')->with($id, [
            'cancel_at_period_end' => false,
            'proration_behavior' => 'create_prorations',
            'items' => [
                [
                    'id' => $lineId,
                    'price' => $priceId,
                ],
            ],
        ]);

        $subscriptionManager = new SubscriptionManager($stripeClient, $stripeConfig);
        $subscriptionManager->change($subscription);
    }
}
