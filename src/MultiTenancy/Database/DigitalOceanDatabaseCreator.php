<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\Database;

use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Cloud\DigitalOcean\ClientInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\MultiTenancy\Dbal\SchemaToolProviderInterface;
use Parthenon\MultiTenancy\Entity\TenantInterface;

class DigitalOceanDatabaseCreator implements DatabaseCreatorInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ClientInterface $client,
        private DatabaseSwitcherInterface $databaseSwitcher,
        private SchemaToolProviderInterface $schemaToolProvider,
        private MigrationsHandlerInterface $migrationsHandler,
        private ManagerRegistry $managerRegistry,
        private string $entityManagerName,
        private string $clusterId,
    ) {
    }

    public function createDatabase(TenantInterface $tenant): void
    {
        $this->getLogger()->info('Creating database', ['cluster_id' => $this->clusterId, 'database' => $tenant->getDatabase()]);
        $this->client->database()->createDatabase($this->clusterId, $tenant->getDatabase());
        $this->databaseSwitcher->switchToTenant($tenant);

        $this->getLogger()->info('Creating database schema');
        $entityManager = $this->managerRegistry->getManager($this->entityManagerName);
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $tool = $this->schemaToolProvider->getSchemaTool($entityManager);

        $tool->createSchema($metaData);

        $this->getLogger()->info('Inserting migrations');
        $this->migrationsHandler->handleMigrations($tenant);
    }
}
