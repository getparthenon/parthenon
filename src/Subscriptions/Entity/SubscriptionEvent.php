<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Entity;

use Parthenon\Subscriptions\Subscriber\SubscriberInterface;

class SubscriptionEvent
{
    public const TYPE_CREATED = 'created';
    public const TYPE_CHANGE_BEFORE = 'change_before';
    public const TYPE_CHANGE_AFTER = 'change_after';
    public const TYPE_CANCELLED = 'cancelled';
    public const TYPE_PAYMENT = 'payment';

    protected $id;

    protected SubscriberInterface $subscriber;

    protected string $type;

    protected string $planName;

    protected array $data;
}
