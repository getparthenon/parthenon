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

use DG\BypassFinals;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class JsonContainsFilterTest extends TestCase
{
    public function testItSetsTheFieldONQueryBuilder()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('getRootAliases')->willReturn(['f', 't']);
        $queryBuilder->expects($this->once())->method('andWhere')->with('JSON_CONTAINS(f.field_name, :field_name) = 1');

        $containsFilter = new JsonContainsFilter();
        $containsFilter->setFieldName('field_name');
        $containsFilter->modifyQueryBuilder($queryBuilder);
    }

    public function testSetParameterQuery()
    {
        BypassFinals::enable();
        $query = $this->createMock(Query::class);
        $query->expects($this->once())->method('setParameter')->with($this->equalTo(':field_name'), $this->equalTo('field_value'));

        $containsFilter = new JsonContainsFilter();
        $containsFilter->setFieldName('field_name')->setData('field_value');
        $containsFilter->modifyQuery($query);
    }
}
