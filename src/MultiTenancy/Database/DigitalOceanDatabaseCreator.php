<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
