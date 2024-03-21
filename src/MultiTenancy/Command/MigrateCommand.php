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

namespace Parthenon\MultiTenancy\Command;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\EntityManagerLoader;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\MultiTenancy\Dbal\TenantConnection;
use Parthenon\MultiTenancy\Entity\Tenant;
use Parthenon\MultiTenancy\Repository\TenantRepositoryInterface;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;
use Parthenon\MultiTenancy\TenantProvider\TestCurrentTenantProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:multi-tenancy:migrate', aliases: ['p:m:m'])]
class MigrateCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
        private TenantProviderInterface $tenantProvider,
        private TenantConnection $tenantConnection,
        private ManagerRegistry $managerRegistry,
        private string $migrationsDirectory,
        private string $entityManagerName,
        private bool $enabled,
    ) {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting Parthenon Multi-Tenancy Migrations');
        $this->getLogger()->info('Starting Parthenon Multi-Tenancy Migrations');

        if (!$this->enabled) {
            $output->writeln('Multi-Tenancy is not enabled');
            $this->getLogger()->warning('Multi-Tenancy is not enabled');

            return 1;
        }

        $this->tenantConnection->setCurrentTenantProvider($this->tenantProvider);

        $lastId = null;
        do {
            $results = $this->tenantRepository->getList(lastId: $lastId);
            /** @var Tenant $result */
            foreach ($results->getResults() as $result) {
                $this->getLogger()->info('Handling migrations for tenant.', ['tenant_subdomain' => $result->getSubdomain()]);
                $output->writeln('Handling migrations for '.$result->getSubdomain());

                TestCurrentTenantProvider::setTenantInfo($result->getDatabase(), $result->getSubdomain());
                $this->tenantConnection->connect(true);

                $this->executeMigrations($input, $output);
                $lastId = $result->getId();
            }
        } while ($results->hasMore());

        return 0;
    }

    protected function getDependencyFactory(): DependencyFactory
    {
        $em = $this->managerRegistry->getManager($this->entityManagerName);
        $a = new Configuration();
        $a->addMigrationsDirectory('DoctrineMigrations', $this->migrationsDirectory);
        $a->setAllOrNothing(false);
        $a->setCheckDatabasePlatform(true);
        $a->setTransactional(true);
        $a->setMetadataStorageConfiguration(new \Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration());

        $configLoader = $this->getConfigLoader($a);
        $emLoader = $this->getEmLoader($em);

        return DependencyFactory::fromEntityManager($configLoader, $emLoader);
    }

    protected function executeMigrations(InputInterface $input, OutputInterface $output)
    {
        $newInput = new ArrayInput([
            'version' => $input->getArgument('version'),
            '--dry-run' => $input->getOption('dry-run'),
        ]);
        $newInput->setInteractive(false);
        $otherCommand = new \Doctrine\Migrations\Tools\Console\Command\MigrateCommand($this->getDependencyFactory());
        $otherCommand->run($newInput, $output);
    }

    protected function getConfigLoader(Configuration $a)
    {
        $configLoader = new class($a) implements ConfigurationLoader {
            public function __construct(private $a)
            {
            }

            public function getConfiguration(): Configuration
            {
                return $this->a;
            }
        };

        return $configLoader;
    }

    protected function getEmLoader(\Doctrine\Persistence\ObjectManager $em)
    {
        $emLoader = new class($em) implements EntityManagerLoader {
            public function __construct(private $em)
            {
            }

            public function getEntityManager(?string $name = null): EntityManagerInterface
            {
                return $this->em;
            }
        };

        return $emLoader;
    }
}
