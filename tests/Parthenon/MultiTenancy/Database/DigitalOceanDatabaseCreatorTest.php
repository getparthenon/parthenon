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

namespace Parthenon\MultiTenancy\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Parthenon\Cloud\DigitalOcean\ClientInterface;
use Parthenon\Cloud\DigitalOcean\DatabaseClientInterface;
use Parthenon\MultiTenancy\Dbal\SchemaToolProviderInterface;
use Parthenon\MultiTenancy\Entity\Tenant;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DigitalOceanDatabaseCreatorTest extends TestCase
{
    public function testCreatesDatabase()
    {
        $tenant = new Tenant();

        $doClient = $this->createMock(ClientInterface::class);
        $dbClient = $this->createMock(DatabaseClientInterface::class);

        $schemaToolProvider = $this->createMock(SchemaToolProviderInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $classMetaFactory = $this->createMock(ClassMetadataFactory::class);
        $schemaTool = $this->createMock(SchemaTool::class);
        $entityManager = $this->createMock(EntityManager::class);
        $databaseSwitcher = $this->createMock(DatabaseSwitcherInterface::class);
        $migrationHandler = $this->createMock(MigrationsHandlerInterface::class);

        $metaData = ['entity_one'];
        $tenant->setDatabase('database');

        $doClient->method('database')->willReturn($dbClient);
        $dbClient->expects($this->once())->method('createDatabase')->with('cluster_id', 'database');

        $databaseSwitcher->expects($this->once())->method('switchToTenant')->with($tenant);

        $managerRegistry->method('getManager')->with('entity_manager')->willReturn($entityManager);

        $entityManager->method('getMetadataFactory')->willReturn($classMetaFactory);
        $classMetaFactory->method('getAllMetaData')->willReturn($metaData);

        $schemaToolProvider->method('getSchemaTool')->with($entityManager)->willReturn($schemaTool);

        $schemaTool->expects($this->once())->method('createSchema')->with($metaData);

        $migrationHandler->expects($this->once())->method('handleMigrations')->with($tenant);

        $create = new DigitalOceanDatabaseCreator($doClient, $databaseSwitcher, $schemaToolProvider, $migrationHandler, $managerRegistry, 'entity_manager', 'cluster_id');
        $create->createDatabase($tenant);
    }
}
