<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena\Filters;

final class ListFilters implements ListFiltersInterface
{
    /**
     * @var FilterConfig[]
     */
    private array $filters = [];
    private FilterManager $filterManager;
    private array $filterInfo = [];

    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function add($name, $type = ContainsFilter::NAME, array $options = []): self
    {
        $this->filters[$name] = new FilterConfig($name, $type, $options);

        return $this;
    }

    /**
     * @return FilterConfig[]
     */
    public function getFilterConfigs(): array
    {
        return $this->filters;
    }

    /**i
     * @return FilterInterface[]
     */
    public function getFilters(?array $data = []): array
    {
        $output = [];
        if (null === $data) {
            return $output;
        }

        if (empty($this->filterInfo)) {
            foreach ($this->filters as $key => $filterConfig) {
                $filter = $this->filterManager->get($filterConfig);
                $filter->setFieldName($key);
                $options = $filterConfig->getOptions();

                if (isset($data[$key])) {
                    $filter->setData($data[$key]);
                } elseif (isset($options['default'])) {
                    $filter->setData($options['default']);
                }

                if ($filter instanceof ChoicesFilterInterface) {
                    if (isset($options['choices'])) {
                        $filter->setChoices($options['choices']);
                    } else {
                        throw new \RuntimeException('No choices provided');
                    }
                }

                $output[] = $filter;
            }

            $this->filterInfo = $output;
        }

        return $this->filterInfo;
    }

    public function hasData(): bool
    {
        $filters = $this->getFilters();

        foreach ($filters as $filter) {
            if ($filter->hasData()) {
                return true;
            }
        }

        return false;
    }
}
