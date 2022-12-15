<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
