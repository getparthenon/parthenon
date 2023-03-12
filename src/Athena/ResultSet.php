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
