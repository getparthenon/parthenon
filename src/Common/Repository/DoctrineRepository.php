<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\ConversionException;
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

        return $entity;
    }
}
