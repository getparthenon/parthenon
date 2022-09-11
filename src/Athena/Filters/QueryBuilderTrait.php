<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

use Doctrine\ORM\QueryBuilder;

trait QueryBuilderTrait
{
    protected string $fieldName;

    protected function getSafeFieldName(): string
    {
        return str_replace('.', '_', $this->fieldName);
    }

    protected function readyQueryBuilderForAliasAndFieldName(QueryBuilder $queryBuilder): array
    {
        $alias = current($queryBuilder->getRootAliases());

        if (!str_contains($this->fieldName, '.')) {
            $fieldName = $this->fieldName;
        } else {
            [$joinName, $fieldName] = explode('.', $this->fieldName, 2);
            $joinAlias = strtolower($this->fieldName[0]);
            $queryBuilder->join($alias.'.'.$joinName, $joinAlias);
            $alias = $joinAlias;
        }

        return [$alias, $fieldName];
    }
}
