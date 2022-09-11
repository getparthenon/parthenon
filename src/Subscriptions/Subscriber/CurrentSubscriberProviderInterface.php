<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Subscriber;

use Parthenon\Subscriptions\Exception\InvalidSubscriberException;

interface CurrentSubscriberProviderInterface
{
    /**
     * @throws InvalidSubscriberException
     */
    public function getSubscriber(): SubscriberInterface;
}
