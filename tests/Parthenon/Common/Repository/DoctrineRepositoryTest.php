<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Repository;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class DoctrineRepositoryTest extends TestCase
{
    public function testSavesToEntityRepository()
    {
        $stdClass = new \stdClass();

        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityManager = $this->createMock(EntityManager::class);

        $entityRepository->method('getEntityManager')->willReturn($entityManager);

        $entityManager->expects($this->once())->method('persist')->with($stdClass);
        $entityManager->expects($this->once())->method('flush');

        $repository = new DoctrineRepository($entityRepository);
        $repository->save($stdClass);
    }
}
