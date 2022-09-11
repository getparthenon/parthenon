<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments;

use Parthenon\Subscriptions\Entity\Subscription;

interface CheckoutManagerInterface
{
    public function createCheckoutForSubscription(Subscription $subscription, array $options = []): CheckoutInterface;

    public function handleSuccess(Subscription $subscription): void;
}
