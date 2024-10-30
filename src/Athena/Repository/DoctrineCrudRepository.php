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

namespace Parthenon\Athena\Repository;

use Parthenon\Athena\Entity\DeletableInterface;
use Parthenon\Athena\Filters\DoctrineFilterInterface;
use Parthenon\Athena\ResultSet;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class DoctrineCrudRepository extends DoctrineRepository implements CrudRepositoryInterface
{
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null, $firstId = null): ResultSet
    {
        $sortKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $sortKey))));

        $qb = $this->createQueryBuilder();

        $direction = 'DESC' === $sortType ? '<' : '>';
        $firstDirection = 'DESC' === $sortType ? '>' : '<';
        if ($firstId) {
            $sortType = ('DESC' === $sortType) ? 'ASC' : 'DESC';
        }

        $sortKey = preg_replace('/[^A-Za-z0-9_]+/', '', $sortKey);
        $em = $this->entityRepository->getEntityManager();
        $columns = $em->getClassMetadata($this->entityRepository->getClassName())->getColumnNames();
        $columns = array_map(function ($colName) {
            return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $colName))));
        }, $columns);

        if (!in_array($sortKey, $columns)) {
            throw new GeneralException("Sort key doesn't exist");
        }

        $qb->orderBy($qb->getRootAliases()[0].'.'.$sortKey, $sortType);

        if ($limit > 0) {
            $qb->setMaxResults($limit + 1); // Fetch one more than required for pagination.
        }

        if ($lastId) {
            $qb->where($qb->getRootAliases()[0].'.'.$sortKey.' '.$direction.' :lastId');
        }
        if ($firstId) {
            $qb->where($qb->getRootAliases()[0].'.'.$sortKey.' '.$firstDirection.' :firstId');
        }

        if (is_a($this->entityRepository->getClassName(), DeletableInterface::class, true)) {
            $qb->andWhere($qb->getRootAliases()[0].'.isDeleted = false');
        }

        foreach ($filters as $filter) {
            if ($filter instanceof DoctrineFilterInterface && $filter->hasData()) {
                $filter->modifyQueryBuilder($qb);
            }
        }
        $query = $qb->getQuery();

        foreach ($filters as $filter) {
            if ($filter instanceof DoctrineFilterInterface && $filter->hasData()) {
                $filter->modifyQuery($query);
            }
        }

        if ($lastId) {
            $query->setParameter(':lastId', $lastId);
        }
        if ($firstId) {
            $query->setParameter(':firstId', $firstId);
        }

        $results = $query->getResult();

        if ($firstId) {
            $results = array_reverse($results);
        }

        return new ResultSet($results, $sortKey, $sortType, $limit);
    }

    public function getById($id, $includeDeleted = false)
    {
        $entity = $this->entityRepository->findOneBy(['id' => $id]);

        if (!$entity || (false == $includeDeleted && is_a($this->entityRepository->getClassName(), DeletableInterface::class, true) && $entity->isDeleted())) {
            throw new NoEntityFoundException();
        }

        return $entity;
    }

    public function delete($entity)
    {
        if (!$entity instanceof DeletableInterface) {
            throw new GeneralException('Excepted deletable entity non given. Must implement '.DeletableInterface::class);
        }
        $entity->markAsDeleted();
        $this->save($entity);
    }

    public function undelete($entity)
    {
        if (!$entity instanceof DeletableInterface) {
            throw new GeneralException('Excepted deletable entity non given. Must implement '.DeletableInterface::class);
        }
        $entity->unmarkAsDeleted();
        $this->save($entity);
    }

    public function getByIds(array $ids): ResultSet
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->getRootAliases()[0].'.id in (:ids)')
            ->setParameter('ids', $ids);

        $query = $qb->getQuery();

        return new ResultSet($query->getResult(), 'id', 'asc', count($ids));
    }
}
