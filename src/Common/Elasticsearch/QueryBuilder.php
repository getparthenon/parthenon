<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
