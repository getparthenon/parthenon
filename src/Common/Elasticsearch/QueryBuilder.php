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

namespace Parthenon\Common\Elasticsearch;

final class QueryBuilder
{
    private array $filters = [];

    private array $queries = [];

    private array $aggregations = [];

    public function query(string $queryType, string $fieldName, mixed $fieldValue): self
    {
        $this->addQuery($queryType, 'must', $fieldName, $fieldValue);

        return $this;
    }

    public function andQuery(string $queryType, string $fieldName, mixed $fieldValue): self
    {
        $this->addQuery($queryType, 'must', $fieldName, $fieldValue);

        return $this;
    }

    public function notQuery(string $queryType, string $fieldName, mixed $fieldValue): self
    {
        $this->addQuery($queryType, 'must_not', $fieldName, $fieldValue);

        return $this;
    }

    public function orQuery(string $queryType, string $fieldName, mixed $fieldValue): self
    {
        $this->addQuery($queryType, 'should', $fieldName, $fieldValue);

        return $this;
    }

    public function filter(string $filterType, string $fieldName, mixed $fieldValue): self
    {
        $this->addFilter($filterType, 'must', $fieldName, $fieldValue);

        return $this;
    }

    public function andFilter(string $filterType, string $fieldName, mixed $fieldValue): self
    {
        $this->addFilter($filterType, 'must', $fieldName, $fieldValue);

        return $this;
    }

    public function notFilter(string $filterType, string $fieldName, mixed $fieldValue): self
    {
        $this->addFilter($filterType, 'must_not', $fieldName, $fieldValue);

        return $this;
    }

    public function orFilter(string $filterType, string $fieldName, mixed $fieldValue): self
    {
        $this->addFilter($filterType, 'should', $fieldName, $fieldValue);

        return $this;
    }

    public function aggregation(string $term, string $fieldName): self
    {
        $this->aggregations[$term] = $fieldName;

        return $this;
    }

    public function build(): array
    {
        $output = [];

        if (1 === sizeof($this->queries) && 0 === sizeof($this->filters) && 0 === sizeof($this->aggregations)) {
            $query = current($this->queries);

            return $this->addAggregation([
                'query' => [
                    $query['queryType'] => [
                        $query['fieldName'] => $query['fieldValue'],
                    ],
                ],
            ]);
        }

        if (1 === sizeof($this->filters)) {
            $filters = current($this->filters);
            $output['bool'] = [
                'filter' => [
                    $filters['filterType'] => [
                        $filters['fieldName'] => $filters['fieldValue'],
                    ],
                ],
            ];

            return $this->addAggregation(['query' => $output]);
        }

        if (sizeof($this->filters) > 1 && sizeof($this->queries) > 1) {
            $output['bool'] = [
            ];
        }

        if (sizeof($this->queries) > 1) {
            $output['bool']['must'] = [];
            foreach ($this->queries as $query) {
                $qualifierType = $query['qualifierType'];
                if (!isset($output['bool'][$qualifierType])) {
                    $output['bool'][$qualifierType] = [];
                }
                $output['bool'][$qualifierType][] = [
                    $query['queryType'] => [
                        $query['fieldName'] => $query['fieldValue'],
                    ],
                ];
            }
        }

        if (sizeof($this->filters) > 1) {
            $output['bool']['filter']['bool'] = [];
            foreach ($this->filters as $filter) {
                $qualifierType = $filter['qualifierType'];
                if (!isset($output['bool']['filter']['bool'][$qualifierType])) {
                    $output['bool']['filter']['bool'][$qualifierType] = [];
                }
                $output['bool']['filter']['bool'][$qualifierType][] = [
                    $filter['filterType'] => [
                        $filter['fieldName'] => $filter['fieldValue'],
                    ],
                ];
            }
        }

        return $this->addAggregation(['query' => $output]);
    }

    private function addAggregation($output)
    {
        if (0 === sizeof($this->aggregations)) {
            return $output;
        }

        $output['aggs'] = [];

        foreach ($this->aggregations as $term => $fieldName) {
            $key = sprintf('agg_%s_%s', $term, $fieldName);
            $output['aggs'][$key] = [$term => ['field' => $fieldName]];
        }

        return $output;
    }

    private function addFilter(string $filterType, string $qualifierType, string $fieldName, mixed $fieldValue): void
    {
        $this->filters[] = [
            'filterType' => $filterType,
            'qualifierType' => $qualifierType,
            'fieldName' => $fieldName,
            'fieldValue' => $fieldValue,
        ];
    }

    private function addQuery(string $queryType, string $qualifierType, string $fieldName, mixed $fieldValue): void
    {
        $this->queries[] = [
            'queryType' => $queryType,
            'qualifierType' => $qualifierType,
            'fieldName' => $fieldName,
            'fieldValue' => $fieldValue,
        ];
    }
}
