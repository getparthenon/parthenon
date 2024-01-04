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
