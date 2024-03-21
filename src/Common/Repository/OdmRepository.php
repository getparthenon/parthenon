<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Common\Repository;

use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Parthenon\Common\Exception\GeneralException;

class OdmRepository implements RepositoryInterface
{
    protected ServiceDocumentRepository $documentRepository;

    public function __construct(ServiceDocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function findById($id)
    {
        $entity = $this->documentRepository->find($id);
        $this->documentRepository->getDocumentManager()->refresh($entity);

        return $entity;
    }

    public function save($entity)
    {
        try {
            $this->documentRepository->getDocumentManager()->persist($entity);
            $this->documentRepository->getDocumentManager()->flush();
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
