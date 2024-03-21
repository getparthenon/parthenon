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
