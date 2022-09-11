<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
