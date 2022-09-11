<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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

        return $this->getFieldData($lastItem, $this->sortKey);
    }
}
