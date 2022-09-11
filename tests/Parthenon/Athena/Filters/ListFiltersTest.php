<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
