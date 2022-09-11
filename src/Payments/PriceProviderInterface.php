<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments;

use Parthenon\Subscriptions\Plan\Plan;

interface PriceProviderInterface
{
    public function getPriceId(Plan $plan, string $paymentSchedule): string;

    public function getPrices(Plan $plan): array;

    public function getTrial(Plan $plan, string $paymentSchedule): int;
}
