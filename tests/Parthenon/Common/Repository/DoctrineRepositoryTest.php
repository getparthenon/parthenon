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
