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

namespace Parthenon\Athena;

use Parthenon\Athena\Exception\InvalidSortKeyException;
use Parthenon\Common\FieldAccesorTrait;

final class ResultSet
{
    use FieldAccesorTrait;
    private array $results;
    private string $sortKey;
    private int $limit;
    private string $sortType;

    public function __construct(array $results, string $sortKey, string $sortType, int $limit)
    {
        $this->results = $results;
        $this->sortKey = $sortKey;
        $this->limit = $limit;
        $this->sortType = $sortType;
    }

    public function getResults(): array
    {
        if ($this->limit < 1) {
            return $this->results;
        }

        return array_slice($this->results, 0, $this->limit);
    }

    public function getSortKey(): string
    {
        return $this->sortKey;
    }

    public function getSortType(): string
    {
        return $this->sortType;
    }

    public function hasMore(): bool
    {
        return count($this->results) > $this->limit;
    }

    public function getFirstKey()
    {
        $results = $this->getResults();
        $lastItem = current($results);
        if (false === $lastItem) {
            return null;
        }

        return $this->getFieldData($lastItem, $this->sortKey);
    }

    /**
     * @throws InvalidSortKeyException
     */
    public function getLastKey()
    {
        $results = $this->getResults();
        $lastItem = end($results);
        if (false === $lastItem) {
            return null;
        }

        return $this->getFieldData($lastItem, $this->sortKey);
    }
}
