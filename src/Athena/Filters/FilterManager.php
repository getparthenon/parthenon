<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
