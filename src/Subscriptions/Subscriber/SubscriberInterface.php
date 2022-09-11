<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Subscriber;

use Parthenon\Subscriptions\Entity\Subscription;

interface SubscriberInterface
{
    public const TYPE_TEAM = 'team';

    public const TYPE_USER = 'user';

    public function setSubscription(Subscription $subscription);

    public function getSubscription(): ?Subscription;

    public function hasActiveSubscription(): bool;

    public function getIdentifier(): string;
}
