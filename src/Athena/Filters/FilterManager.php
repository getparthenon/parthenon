<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
