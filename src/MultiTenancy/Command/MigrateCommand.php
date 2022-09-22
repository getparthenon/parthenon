<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->setName('parthenon:multi-tenancy:migrate')
            ->setAliases(['p:m:m'])
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
        $a = new \Doctrine\Migrations\Configuration\Configuration();
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
        $configLoader = new class ($a) implements ConfigurationLoader {
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
        $emLoader = new class ($em) implements EntityManagerLoader {
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
