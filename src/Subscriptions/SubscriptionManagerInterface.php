<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions;

use Parthenon\Subscriptions\Entity\Subscription;

interface SubscriptionManagerInterface
{
    public function cancel(Subscription $subscription);

    public function change(Subscription $subscription);

    public function syncStatus(Subscription $subscription): Subscription;

    public function getInvoiceUrl(Subscription $subscription);

    public function getBillingPortal(Subscription $subscription): string;
}
