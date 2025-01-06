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

namespace Parthenon\Common\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\QueryBuilder;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\NoEntityFoundException;

class DoctrineRepository implements RepositoryInterface
{
    protected CustomServiceRepository $entityRepository;

    public function __construct(CustomServiceRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    public function save($entity)
    {
        try {
            $em = $this->entityRepository->getEntityManager();
            $em->persist($entity);
            $em->flush();
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findById($id)
    {
        try {
            $entity = $this->entityRepository->find($id);
        } catch (ConversionException $exception) {
            throw new NoEntityFoundException('Invalid id', previous: $exception);
        } catch (Exception $exception) {
            throw new GeneralException('Issue with Doctrine', previous: $exception);
        }

        if (!$entity) {
            throw new NoEntityFoundException('No entity found for id '.$id);
        }

        $this->entityRepository->getEntityManager()->refresh($entity);

        return $entity;
    }

    /**
     * Returns the name used to create the query builder.
     */
    protected function getQueryBuilderName(): string
    {
        $parts = explode('\\', $this->entityRepository->getClassName());
        $name = end($parts);

        return $name;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        $name = $this->getQueryBuilderName();

        return $this->entityRepository->createQueryBuilder($name);
    }
}
