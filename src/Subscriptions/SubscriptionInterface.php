<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions;

interface SubscriptionInterface
{
    public function getPaymentId();

    public function getPriceId();

    public function isActive(): bool;

    public function getPlanName(): ?string;

    public function getValidUntil(): ?\DateTimeInterface;
}
