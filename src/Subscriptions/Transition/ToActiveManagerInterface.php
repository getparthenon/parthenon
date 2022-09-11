<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Transition;

use Parthenon\Subscriptions\Subscriber\SubscriberInterface;

interface ToActiveManagerInterface
{
    public function addTransition(ToActiveTransitionInterface $activeTransition): void;

    public function transition(SubscriberInterface $subscriber): void;
}
