<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Filters;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class GreaterThanFilter implements DoctrineFilterInterface
{
    use QueryBuilderTrait;

    public const NAME = 'greater_than';

    protected string $fieldName;

    private $data;

    public function modifyQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (!$this->data) {
            return;
        }
        [$alias, $fieldName] = $this->readyQueryBuilderForAliasAndFieldName($queryBuilder);
        $queryBuilder->andWhere($alias.'.'.$fieldName.' > :'.$this->getSafeFieldName());
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
