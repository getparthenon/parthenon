<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
