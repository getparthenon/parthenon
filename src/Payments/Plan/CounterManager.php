<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Plan;

use Parthenon\Payments\Exception\NoCounterException;

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
