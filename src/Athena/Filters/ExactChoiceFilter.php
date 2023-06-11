<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
