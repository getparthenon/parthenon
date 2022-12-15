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

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\EntityManagerLoader;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationsHandler implements MigrationsHandlerInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private string $migrationsDirectory,
        private string $entityManagerName
    ) {
    }

    public function handleMigrations(TenantInterface $tenant): void
    {
        $newInput = new ArrayInput([]);

        $newInput->setInteractive(false);
        $otherCommand = new \Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand($this->getDependencyFactory());
        $otherCommand->run($newInput, new NullOutput());

        $newInput = new ArrayInput([
                '--add' => true,
                '--all' => true,
        ]);

        $newInput->setInteractive(false);
        $otherCommand = new \Doctrine\Migrations\Tools\Console\Command\VersionCommand($this->getDependencyFactory());
        $otherCommand->run($newInput, new NullOutput());
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
