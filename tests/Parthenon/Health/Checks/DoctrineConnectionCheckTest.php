<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Health\Checks;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DoctrineConnectionCheckTest extends TestCase
{
    public function testReturnsFalseIfException()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willThrowException(new \Exception());

        $check = new DoctrineConnectionCheck($entityManager);
        $this->assertFalse($check->getStatus());
    }

    public function testReturnsFalseIfConnectionFails()
    {
        $connection = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willReturn($connection);

        $connection->method('isConnected')->willReturn(false);
        $connection->method('connect')->willReturn(false);

        $check = new DoctrineConnectionCheck($entityManager);
        $this->assertFalse($check->getStatus());
    }

    public function testReturnsTrueIfCanConnect()
    {
        $connection = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willReturn($connection);

        $connection->method('isConnected')->willReturn(false);
        $connection->method('connect')->willReturn(true);

        $check = new DoctrineConnectionCheck($entityManager);
        $this->assertTrue($check->getStatus());
    }

    public function testReturnsTrueIfIsConnected()
    {
        $connection = $this->createMock(Connection::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willReturn($connection);

        $connection->method('isConnected')->willReturn(true);
        $connection->expects($this->never())->method('connect')->willReturn(false);

        $check = new DoctrineConnectionCheck($entityManager);
        $this->assertTrue($check->getStatus());
    }
}
