<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\DependencyInjection\Modules;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Cloud implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->arrayNode('cloud')
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->arrayNode('digitalocean')
                    ->children()
                        ->scalarNode('api_key')->end()
                    ?->end()
                ->end()
        ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_cloud_digitalocean_apikey', '');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));

        if (!isset($config['cloud']) || !isset($config['cloud']['enabled']) || false === $config['cloud']['enabled']) {
            return;
        }
        $loader->load('services/cloud.xml');
        $this->handleDigitalOcean($config, $container);
    }

    private function handleDigitalOcean(array $config, ContainerBuilder $containerBuilder): void
    {
        if (!isset($config['cloud']['digitalocean'])) {
            return;
        }

        $digitalOceanConfig = $config['cloud']['digitalocean'];

        $containerBuilder->setParameter('parthenon_cloud_digitalocean_apikey', $digitalOceanConfig['api_key'] ?? '');
    }
}
