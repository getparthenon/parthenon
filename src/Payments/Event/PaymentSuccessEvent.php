<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Payments\Event;

use Parthenon\Subscriptions\Subscriber\SubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PaymentSuccessEvent extends Event
{
    public const NAME = 'parthenon.payments.payment.success';

    public function __construct(private SubscriberInterface $subscriber)
    {
    }

    public function getSubscriber(): SubscriberInterface
    {
        return $this->subscriber;
    }
}
