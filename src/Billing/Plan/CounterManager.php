<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Billing\Plan;

use Parthenon\Billing\Exception\NoCounterException;

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
