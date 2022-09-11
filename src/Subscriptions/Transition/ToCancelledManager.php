<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Transition;

use Parthenon\Subscriptions\Subscriber\SubscriberInterface;

final class ToCancelledManager implements ToCancelledManagerInterface
{
    /**
     * @var ToCancelledTransitionInterface[]
     */
    private array $transitions = [];

    public function addTransition(ToCancelledTransitionInterface $activeTransition): void
    {
        $this->transitions[] = $activeTransition;
    }

    public function transition(SubscriberInterface $subscriber): void
    {
        foreach ($this->transitions as $transition) {
            $transition->transition($subscriber);
        }
    }
}
