<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Filters;

use PHPUnit\Framework\TestCase;

class ListFiltersTest extends TestCase
{
    public function testReturnsArrayOfFilters()
    {
        $filterManager = $this->createMock(FilterManager::class);

        $listFilters = new ListFilters($filterManager);
        $listFilters->add('field');

        $config = $listFilters->getFilterConfigs();

        $this->assertIsArray($config);
        $this->assertCount(1, $config);
        $this->assertContainsOnlyInstancesOf(FilterConfig::class, $config);
        $filterNames = [];

        foreach ($config as $filter) {
            $filterNames[] = $filter->getName();
        }

        $this->assertEquals(['field'], $filterNames);
    }

    public function testReturnsFilledFiltersWhenFilled()
    {
        $data = [
            'name' => 'Iain',
            'email' => 'iain',
        ];

        $filterManager = $this->createMock(FilterManager::class);
        $filterManager->method('get')->with($this->isInstanceOf(FilterConfig::class))->willReturn(new ContainsFilter());

        $listFilters = new ListFilters($filterManager);
        $listFilters->add('name');
        $listFilters->add('email');

        $filters = $listFilters->getFilters($data);
        $this->assertCount(2, $filters);
        $this->assertContainsOnlyInstancesOf(FilterInterface::class, $filters);
    }
}
