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

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\MultiTenancy\Creator\MessengerTenantCreator;
use Parthenon\MultiTenancy\Creator\TenantCreatorInterface;
use Parthenon\MultiTenancy\Database\DatabaseCreatorInterface;
use Parthenon\MultiTenancy\Database\DbalDatabaseCreator;
use Parthenon\MultiTenancy\Database\DigitalOceanDatabaseCreator;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class MultiTenancy implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('multi_tenancy')
                ->children()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->booleanNode('background_creation')->defaultFalse()->end()
                    ->scalarNode('domain')->end()
                    ->arrayNode('doctrine')
                        ->children()
                            ->scalarNode('dbal_connection')->end()
                            ->scalarNode('global_dbal_connection')->end()
                            ->scalarNode('orm_entity_manager')->end()
                            ->scalarNode('default_database')->end()
                        ->end()
                    ->end()
                    ->scalarNode('database_creation_strategy')->end()
                    ->arrayNode('migrations')
                        ->children()
                            ->scalarNode('directory')->end()
                        ->end()
                    ->end()
                    ->arrayNode('digitalocean')
                        ->children()
                            ->scalarNode('cluster_id')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_multi_tenancy_domain', '');
        $container->setParameter('parthenon_multi_tenancy_migrations_directory', '');
        $container->setParameter('parthenon_multi_tenancy_enabled', false);
        $container->setParameter('parthenon_multi_tenancy_background_creation', false);
        $container->setParameter('parthenon_multi_tenancy_dbal_connection', '');
        $container->setParameter('parthenon_multi_tenancy_global_dbal_connection', '');
        $container->setParameter('parthenon_multi_tenancy_orm_entity_manager', '');
        $container->setParameter('parthenon_multi_tenancy_default_database', 'dummy_database');
        $container->setParameter('parthenon_multi_tenancy_digitalocean_cluster_id', '');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['multi_tenancy']['enabled']) || !$config['multi_tenancy']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/multi_tenancy.xml');

        $this->configureMultiTenancy($config, $container);

        $bundles = $container->getParameter('kernel.bundles');

        $bundles = $this->configureDoctrine($bundles, $loader);

        $this->configureMongoDb($bundles, $loader);
    }

    private function configureMultiTenancy(array $config, ContainerBuilder $container): void
    {
        if (isset($config['multi_tenancy'])) {
            $multiTenancyConfig = $config['multi_tenancy'];
            $enabled = $multiTenancyConfig['enabled'] ?? false;
            $backgroundCreation = $multiTenancyConfig['background_creation'] ?? false;

            if (!isset($config['user']['teams_enabled']) || true !== $config['user']['teams_enabled']) {
                throw new GeneralException('user.teams_enabled needs to be true to use multi tenancy');
            }

            $container->setParameter('parthenon_multi_tenancy_enabled', $enabled);
            $container->setParameter('parthenon_multi_tenancy_background_creation', $backgroundCreation);

            if (isset($multiTenancyConfig['domain'])) {
                $container->setParameter('parthenon_multi_tenancy_domain', $multiTenancyConfig['domain']);
            } elseif (!isset($multiTenancyConfig['domain']) && $enabled) {
                throw new ParameterNotSetException('parthenon.multi_tenancy.domain must be set when multi_tenancy is enabled');
            }

            $this->configureMigrations($config, $container);
            $this->configureDoctrineConfig($config, $container);
            $this->configureTenantCreator($backgroundCreation, $container);

            $this->handleDatabaseCreationStrategy($multiTenancyConfig['database_creation_strategy'] ?? 'dbal', $container);

            $container->setParameter('parthenon_multi_tenancy_digitalocean_cluster_id', $multiTenancyConfig['digitalocean']['cluster_id'] ?? '');
        }
    }

    private function configureTenantCreator(bool $creatorBackground, ContainerBuilder $containerBuilder): void
    {
        if ($creatorBackground) {
            $containerBuilder->setAlias(TenantCreatorInterface::class, MessengerTenantCreator::class);
        }
    }

    private function configureMigrations(array $config, ContainerBuilder $containerBuilder): void
    {
        if (!isset($config['multi_tenancy']['migrations'])) {
            return;
        }

        $containerBuilder->setParameter('parthenon_multi_tenancy_migrations_directory', $config['multi_tenancy']['migrations']['directory'] ?? '');
    }

    private function configureDoctrineConfig(array $config, ContainerBuilder $containerBuilder): void
    {
        if (!isset($config['multi_tenancy']['doctrine'])) {
            return;
        }

        $containerBuilder->setParameter('parthenon_multi_tenancy_global_dbal_connection', $config['multi_tenancy']['doctrine']['global_dbal_connection'] ?? '');
        $containerBuilder->setParameter('parthenon_multi_tenancy_dbal_connection', $config['multi_tenancy']['doctrine']['dbal_connection'] ?? '');
        $containerBuilder->setParameter('parthenon_multi_tenancy_orm_entity_manager', $config['multi_tenancy']['doctrine']['orm_entity_manager'] ?? '');
        $containerBuilder->setParameter('parthenon_multi_tenancy_default_database', $config['multi_tenancy']['doctrine']['default_database'] ?? 'dummy_database');
    }

    /**
     * @throws \Exception
     */
    private function configureDoctrine(float|array|bool|int|string|null $bundles, XmlFileLoader $loader): string|int|bool|array|null|float
    {
        if (isset($bundles['DoctrineBundle'])) {
            $loader->load('services/orm/multi_tenancy.xml');
        }

        return $bundles;
    }

    /**
     * @throws \Exception
     */
    private function configureMongoDb(float|int|bool|array|string|null $bundles, XmlFileLoader $loader): void
    {
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $loader->load('services/odm/multi_tenancy.xml');
        }
    }

    /**
     * @throws ParameterNotSetException
     */
    private function handleDatabaseCreationStrategy($database_creation_strategy, ContainerBuilder $containerBuilder): void
    {
        $databaseCreationStrategy = strtolower($database_creation_strategy);

        switch ($databaseCreationStrategy) {
            case 'dbal':
                $id = DbalDatabaseCreator::class;
                break;
            case 'digitalocean':
                $id = DigitalOceanDatabaseCreator::class;
                break;
            default:
                throw new ParameterNotSetException(sprintf("'%s' is not a valid option for multi_tenancy.database_creation_strategy", $databaseCreationStrategy));
        }

        $containerBuilder->setAlias(DatabaseCreatorInterface::class, $id);
    }
}
