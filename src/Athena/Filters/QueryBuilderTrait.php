<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
