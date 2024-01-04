<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
        } elseif (str_starts_with($this->fieldName, '.')) {
            $fieldName = substr($this->fieldName, 1);
        } else {
            [$joinName, $fieldName] = explode('.', $this->fieldName, 2);
            $joinAlias = strtolower($this->fieldName[0]);
            $queryBuilder->join($alias.'.'.$joinName, $joinAlias);
            $alias = $joinAlias;
        }

        return [$alias, $fieldName];
    }
}
