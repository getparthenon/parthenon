<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
