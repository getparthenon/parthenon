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

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function testDealsWithSingleQuery()
    {
        $qb = new QueryBuilder();

        $output = $qb->query('match', 'fieldName', 'fieldValue')
            ->build();

        $this->assertEquals(['query' => ['match' => ['fieldName' => 'fieldValue']]], $output);
    }

    public function testDealsWithSingleFilterAggreation()
    {
        $qb = new QueryBuilder();

        $output = $qb->filter('term', 'fieldName', 'fieldValue')
            ->aggregation('term', 'fieldName')
            ->build();

        $this->assertEquals([
            'query' => [
                'bool' => [
                    'filter' => [
                        'term' => [
                            'fieldName' => 'fieldValue',
                        ],
                    ],
                ],
            ],
            'aggs' => [
                'agg_term_fieldName' => [
                    'term' => [
                        'field' => 'fieldName',
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithTwoQueries()
    {
        $qb = new QueryBuilder();

        $output = $qb->query('match', 'fieldName', 'fieldValue')
                ->query('match', 'secondField', 'secondValue')
                ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'match' => ['fieldName' => 'fieldValue'],
                        ],
                        [
                            'match' => ['secondField' => 'secondValue'],
                        ],
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithTwoQueriesAndTwoNotQueries()
    {
        $qb = new QueryBuilder();

        $output = $qb->query('match', 'fieldName', 'fieldValue')
            ->query('match', 'secondField', 'secondValue')
            ->notQuery('match', 'thirdField', 'thirdValue')
            ->notQuery('match', 'fourField', 'fourValue')
            ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'match' => ['fieldName' => 'fieldValue'],
                        ],
                        [
                            'match' => ['secondField' => 'secondValue'],
                        ],
                    ],
                    'must_not' => [
                        [
                            'match' => ['thirdField' => 'thirdValue'],
                        ],
                        [
                            'match' => ['fourField' => 'fourValue'],
                        ],
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithTwoQueriesAndTwoOrQueries()
    {
        $qb = new QueryBuilder();

        $output = $qb->query('match', 'fieldName', 'fieldValue')
            ->query('match', 'secondField', 'secondValue')
            ->orQuery('match', 'thirdField', 'thirdValue')
            ->orQuery('match', 'fourField', 'fourValue')
            ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'match' => ['fieldName' => 'fieldValue'],
                        ],
                        [
                            'match' => ['secondField' => 'secondValue'],
                        ],
                    ],
                    'should' => [
                        [
                            'match' => ['thirdField' => 'thirdValue'],
                        ],
                        [
                            'match' => ['fourField' => 'fourValue'],
                        ],
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithFiltersAndTwoQueries()
    {
        $qb = new QueryBuilder();

        $output = $qb->filter('term', 'fieldName', 'fieldValue')
            ->filter('term', 'secondField', 'secondValue')
            ->query('match', 'fieldName', 'fieldValue')
            ->query('match', 'secondField', 'secondValue')
            ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'match' => ['fieldName' => 'fieldValue'],
                        ],
                        [
                            'match' => ['secondField' => 'secondValue'],
                        ],
                    ],
                    'filter' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => ['fieldName' => 'fieldValue'],
                                ],
                                [
                                    'term' => ['secondField' => 'secondValue'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithTwoFiltersAndTwoNotFilters()
    {
        $qb = new QueryBuilder();

        $output = $qb->filter('term', 'fieldName', 'fieldValue')
            ->filter('term', 'secondField', 'secondValue')
            ->notFilter('term', 'thirdField', 'thirdValue')
            ->notFilter('term', 'fourField', 'fourValue')
            ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => ['fieldName' => 'fieldValue'],
                                ],
                                [
                                    'term' => ['secondField' => 'secondValue'],
                                ],
                            ],
                            'must_not' => [
                                [
                                    'term' => ['thirdField' => 'thirdValue'],
                                ],
                                [
                                    'term' => ['fourField' => 'fourValue'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $output);
    }

    public function testDealsWithTwoFiltersAndTwoOrFilters()
    {
        $qb = new QueryBuilder();

        $output = $qb->filter('term', 'fieldName', 'fieldValue')
            ->filter('term', 'secondField', 'secondValue')
            ->orFilter('term', 'thirdField', 'thirdValue')
            ->orFilter('term', 'fourField', 'fourValue')
            ->build();
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => ['fieldName' => 'fieldValue'],
                                ],
                                [
                                    'term' => ['secondField' => 'secondValue'],
                                ],
                            ],
                            'should' => [
                                [
                                    'term' => ['thirdField' => 'thirdValue'],
                                ],
                                [
                                    'term' => ['fourField' => 'fourValue'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $output);
    }
}
