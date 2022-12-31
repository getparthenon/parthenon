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
