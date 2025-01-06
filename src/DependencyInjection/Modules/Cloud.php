<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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
