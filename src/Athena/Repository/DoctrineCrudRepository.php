<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
    public function getList(array $filters = [], string $sortKey = 'id', string $sortType = 'ASC', int $limit = self::LIMIT, $lastId = null): ResultSet
    {
        $sortKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $sortKey))));

        $qb = $this->createQueryBuilder();

        $direction = 'DESC' === $sortType ? '<' : '>';
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

        return new ResultSet($query->getResult(), $sortKey, $sortType, $limit);
    }

    /**
     * {@inheritdoc}
     */
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
