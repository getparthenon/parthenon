<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Subscriber;

use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Plan\Plan;

interface SubscriptionFactoryInterface
{
    public function createFromPlanAndPaymentSchedule(Plan $plan, string $paymentSchedule): Subscription;
}
