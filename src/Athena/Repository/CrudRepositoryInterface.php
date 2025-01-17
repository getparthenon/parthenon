<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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
     * @throws NoEntityFoundException
     */
    public function getById($id, $includeDeleted = false);

    public function getByIds(array $ids): ResultSet;

    public function save($entity);

    public function delete($entity);

    public function undelete($entity);
}
