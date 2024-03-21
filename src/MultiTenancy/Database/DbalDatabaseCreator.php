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

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\MultiTenancy\Dbal\SchemaToolProviderInterface;
use Parthenon\MultiTenancy\Entity\TenantInterface;

class DbalDatabaseCreator implements DatabaseCreatorInterface
{
    public function __construct(
        private SchemaToolProviderInterface $schemaToolProvider,
        private ManagerRegistry $managerRegistry,
        private Connection $globalConnection,
        private DatabaseSwitcherInterface $databaseSwitcher,
        private string $entityManagerName,
        private MigrationsHandlerInterface $migrationsHandler,
    ) {
    }

    public function createDatabase(TenantInterface $tenant): void
    {
        $this->globalConnection->createSchemaManager()->createDatabase($tenant->getDatabase());
        $this->databaseSwitcher->switchToTenant($tenant);

        $entityManager = $this->managerRegistry->getManager($this->entityManagerName);
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $tool = $this->schemaToolProvider->getSchemaTool($entityManager);

        $tool->createSchema($metaData);

        $this->migrationsHandler->handleMigrations($tenant);
    }
}
