<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
