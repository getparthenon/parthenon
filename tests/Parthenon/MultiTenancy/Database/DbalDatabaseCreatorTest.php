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

namespace Parthenon\MultiTenancy\Database;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Parthenon\MultiTenancy\Dbal\SchemaToolProviderInterface;
use Parthenon\MultiTenancy\Dbal\TenantConnection;
use Parthenon\MultiTenancy\Entity\Tenant;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DbalDatabaseCreatorTest extends TestCase
{
    public function testCreatesDatabase()
    {
        $tenant = new Tenant();
        $schemaToolProvider = $this->createMock(SchemaToolProviderInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $tenantConnection = $this->createMock(TenantConnection::class);
        $classMetaFactory = $this->createMock(ClassMetadataFactory::class);
        $schemaTool = $this->createMock(SchemaTool::class);
        $entityManager = $this->createMock(EntityManager::class);
        $abstractSchemaManager = $this->createMock(AbstractSchemaManager::class);
        $databaseSwitcher = $this->createMock(DatabaseSwitcherInterface::class);
        $migrationHandler = $this->createMock(MigrationsHandlerInterface::class);

        $metaData = ['entity_one'];
        $tenant->setDatabase('database');

        $tenantConnection->method('createSchemaManager')->willReturn($abstractSchemaManager);

        $databaseSwitcher->expects($this->once())->method('switchToTenant')->with($tenant);

        $abstractSchemaManager->expects($this->once())->method('createDatabase')->with('database');

        $managerRegistry->method('getManager')->with('entity_manager')->willReturn($entityManager);

        $entityManager->method('getMetadataFactory')->willReturn($classMetaFactory);
        $classMetaFactory->method('getAllMetaData')->willReturn($metaData);

        $schemaToolProvider->method('getSchemaTool')->with($entityManager)->willReturn($schemaTool);

        $schemaTool->expects($this->once())->method('createSchema')->with($metaData);

        $migrationHandler->expects($this->once())->method('handleMigrations')->with($tenant);

        $create = new DbalDatabaseCreator($schemaToolProvider, $managerRegistry, $tenantConnection, $databaseSwitcher, 'entity_manager', $migrationHandler);
        $create->createDatabase($tenant);
    }
}
