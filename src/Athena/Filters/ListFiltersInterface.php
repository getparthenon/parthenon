<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

interface ListFiltersInterface
{
    public function add($name, $type = ContainsFilter::NAME, array $options = []): ListFilters;

    /**
     * @return FilterConfig[]
     */
    public function getFilterConfigs(): array;

    /**i
     * @return FilterInterface[]
     */
    public function getFilters(?array $data = []): array;

    public function hasData(): bool;
}
