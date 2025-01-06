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

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class GreaterThanOrEqualFilter implements DoctrineFilterInterface
{
    use QueryBuilderTrait;

    public const NAME = 'greater_than_or_equal';

    protected string $fieldName;

    private $data;

    public function modifyQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (!$this->data) {
            return;
        }
        [$alias, $fieldName] = $this->readyQueryBuilderForAliasAndFieldName($queryBuilder);
        $queryBuilder->andWhere($alias.'.'.$fieldName.' >= :'.$this->getSafeFieldName());
    }

    public function modifyQuery(Query $query)
    {
        if (!$this->data) {
            return;
        }
        $query->setParameter(':'.$this->getSafeFieldName(), $this->data);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function setData($data): FilterInterface
    {
        $this->data = $data;

        return $this;
    }

    public function setFieldName(string $fieldName): FilterInterface
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getHeaderName(): string
    {
        return ucwords(str_replace('_', ' ', $this->fieldName));
    }

    public function getData()
    {
        return $this->data;
    }

    public function hasData(): bool
    {
        return isset($this->data);
    }
}
