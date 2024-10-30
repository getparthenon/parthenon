<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

final class ExactChoiceFilter implements DoctrineFilterInterface, ChoicesFilterInterface, OdmFilterInterface
{
    use QueryBuilderTrait;

    public const NAME = 'exact_choice';

    protected string $fieldName;

    private mixed $data;

    private array $choices = [];

    public function getName(): string
    {
        return self::NAME;
    }

    public function setData($data): FilterInterface
    {
        $this->data = $data;

        return $this;
    }

    public function hasData(): bool
    {
        return isset($this->data) && !empty($this->data);
    }

    public function setFieldName(string $fieldName): FilterInterface
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function modifyQueryBuilder(QueryBuilder $queryBuilder): void
    {
        [$alias, $fieldName] = $this->readyQueryBuilderForAliasAndFieldName($queryBuilder);
        $queryBuilder->andWhere($alias.'.'.$fieldName.' = :'.$this->getSafeFieldName());
    }

    public function modifyQuery(Query $query): void
    {
        $query->setParameter(':'.$this->getSafeFieldName(), $this->data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getHeaderName(): string
    {
        return ucwords(str_replace('_', ' ', $this->fieldName));
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): ChoicesFilterInterface
    {
        $this->choices = $choices;

        return $this;
    }

    public function modifiyOdmQueryBuilder(Builder $builder): Builder
    {
        $builder->field($this->fieldName)->equals($this->data);

        return $builder;
    }
}
