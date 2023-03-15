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
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null, $firstId = null): ResultSet;

    /**
     * @param mixed $id
     *
     * @return mixed
     *
     * @throws NoEntityFoundException
     */
    public function getById($id, $includeDeleted = false);

    public function getByIds(array $ids): ResultSet;

    public function save($entity);

    public function delete($entity);

    public function undelete($entity);
}
