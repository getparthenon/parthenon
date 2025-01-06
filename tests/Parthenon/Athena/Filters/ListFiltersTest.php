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
