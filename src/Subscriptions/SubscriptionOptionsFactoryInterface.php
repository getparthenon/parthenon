<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions;

use Parthenon\Subscriptions\Plan\Plan;

interface SubscriptionOptionsFactoryInterface
{
    public function getOptions(Plan $plan, string $paymentSchedule): array;
}
