<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
