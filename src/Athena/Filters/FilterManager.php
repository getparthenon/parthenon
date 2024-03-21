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

namespace Parthenon\Athena\Filters;

use Parthenon\Athena\Exception\InvalidFilterException;

class FilterManager
{
    /**
     * @var FilterInterface[]
     */
    private array $filters = [];

    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function get(FilterConfig $filterConfig): FilterInterface
    {
        $filterName = $filterConfig->getType();

        foreach ($this->filters as $filter) {
            if ($filterName === $filter->getName()) {
                return clone $filter;
            }
        }

        throw new InvalidFilterException('No filter found for '.$filterName);
    }
}
