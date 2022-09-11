<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Plan;

use Parthenon\Subscriptions\Exception\NoCounterException;

final class CounterManager
{
    /**
     * @var CounterInterface[]
     */
    private array $counters = [];

    public function add(CounterInterface $counter): self
    {
        $this->counters[] = $counter;

        return $this;
    }

    public function getCounter(LimitableInterface $limitable): CounterInterface
    {
        foreach ($this->counters as $counter) {
            if ($counter->supports($limitable)) {
                return $counter;
            }
        }

        throw new NoCounterException('No counter for limitable');
    }
}
