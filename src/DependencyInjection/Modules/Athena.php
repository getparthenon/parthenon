<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\Athena\DashboardSectionInterface;
use Parthenon\Athena\Filters\FilterInterface;
use Parthenon\Athena\SectionInterface;
use Parthenon\Athena\ViewType\ViewTypeInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Athena implements ModuleConfigurationInterface
{
    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_athena_host', null);
        $container->setParameter('parthenon_athena_login_logo', null);
        $container->setParameter('parthenon_athena_dashboard_logo', null);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['athena']) || !isset($config['athena']['enabled']) || false == $config['athena']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/athena.xml');

        $this->configureAutotagging($container);

        $config = $this->configureHost($config, $container);
        $config = $this->configureLoginLog($config, $container);
        $this->configureDashboardLogo($config, $container);
    }

    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('athena')
                ->children()
                    ->booleanNode('enabled')->end()
                    ->scalarNode('host')->end()
                    ->scalarNode('login_logo')->end()
                    ->scalarNode('dashboard_logo')->end()
                ->end()
            ->end();
    }

    private function configureAutotagging(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(FilterInterface::class)->addTag('parthenon.athena.filter');
        $container->registerForAutoconfiguration(ViewTypeInterface::class)->addTag('parthenon.athena.view_type');
        $container->registerForAutoconfiguration(SectionInterface::class)->addTag('parthenon.athena.section');
        $container->registerForAutoconfiguration(DashboardSectionInterface::class)->addTag('parthenon.athena.dashboard_section');
        $container->registerForAutoconfiguration(AthenaControllerInterface::class)->addTag('parthenon.athena.controller');
    }

    private function configureHost(array $config, ContainerBuilder $container): array
    {
        if (isset($config['athena']['host'])) {
            $container->setParameter('parthenon_athena_host', $config['athena']['host']);
        }

        return $config;
    }

    private function configureLoginLog(array $config, ContainerBuilder $container): array
    {
        if (isset($config['athena']['login_logo'])) {
            $container->setParameter('parthenon_athena_login_logo', $config['athena']['login_logo']);
        }

        return $config;
    }

    private function configureDashboardLogo(array $config, ContainerBuilder $container): void
    {
        if (isset($config['athena']['dashboard_logo'])) {
            $container->setParameter('parthenon_athena_dashboard_logo', $config['athena']['dashboard_logo']);
        }
    }
}
