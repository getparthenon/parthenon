<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Repository;

use Parthenon\Athena\Filters\FilterInterface;
use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;

interface CrudRepositoryInterface extends RepositoryInterface
{
    public const LIMIT = 10;

    /**
     * @param FilterInterface[] $filters
     * @param mixed|null        $lastId
     */
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet;

    /**
     * @param mixed $id
     *
     * @return mixed
     *
     * @throws NoEntityFoundException
     */
    public function getById($id, $includeDeleted = false);

    public function save($entity);

    public function delete($entity);

    public function undelete($entity);
}
