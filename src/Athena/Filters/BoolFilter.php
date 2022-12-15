<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Filters;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

final class BoolFilter implements DoctrineFilterInterface, OdmFilterInterface
{
    use QueryBuilderTrait;

    public const NAME = 'boolean';

    protected string $fieldName;

    private $data;

    public function getName(): string
    {
        return self::NAME;
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

    public function modifyQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (!$this->data) {
            return;
        }
        [$alias, $fieldName] = $this->readyQueryBuilderForAliasAndFieldName($queryBuilder);
        $queryBuilder->andWhere($alias.'.'.$fieldName.' = :'.$this->getSafeFieldName());
    }

    public function modifyQuery(Query $query)
    {
        if (!$this->data) {
            return;
        }
        $booleanAsInt = (int) ('true' === strtolower($this->data));
        $query->setParameter(':'.$this->getSafeFieldName(), $booleanAsInt);
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

    public function hasData(): bool
    {
        return isset($this->data) && !empty($this->data);
    }

    public function modifiyOdmQueryBuilder(Builder $builder): Builder
    {
        $boolValue = ('true' === strtolower($this->data));
        $builder->field($this->fieldName)->equals($boolValue);

        return $builder;
    }
}
