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

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Common\Exception\MissingDependencyException;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\Engine\BackgroundDownloadEngine;
use Parthenon\Export\Engine\BackgroundEmailEngine;
use Parthenon\Export\Engine\DirectDownloadEngine;
use Parthenon\Export\Engine\EngineInterface;
use Parthenon\Export\Exporter\ExporterInterface;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Export implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('export')
                ->children()
                    ->booleanNode('enabled')->defaultFalse()->end()
                    ->booleanNode('default_engine')->end()
                    ->booleanNode('user_provider')->end()
                ->end()
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['export']) || !isset($config['export']['enabled']) || false == $config['export']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/export.xml');

        $container->registerForAutoconfiguration(NormaliserInterface::class)->addTag('parthenon.export.normaliser');
        $container->registerForAutoconfiguration(ExporterInterface::class)->addTag('parthenon.export.exporter');
        $container->registerForAutoconfiguration(DataProviderInterface::class)->addTag('parthenon.export.data_provider');

        $bundles = $container->getParameter('kernel.bundles');

        $this->configureMongoDb($bundles, $loader);
        $this->configureDoctrine($bundles, $loader);

        if (isset($config['export']['default_engine'])) {
            $defaultEngine = $config['export']['default_engine'];

            if (DirectDownloadEngine::NAME === $defaultEngine) {
                $container->setAlias(EngineInterface::class, DirectDownloadEngine::class);
            } elseif (BackgroundEmailEngine::class === $defaultEngine) {
                $container->setAlias(EngineInterface::class, BackgroundEmailEngine::class);
            } elseif (BackgroundDownloadEngine::NAME === $defaultEngine) {
                $container->setAlias(EngineInterface::class, BackgroundDownloadEngine::class);
            }
        }

        if (isset($config['export']['user_provider'])) {
            if (!$container->hasDefinition($config['export']['user_provider'])) {
                throw new MissingDependencyException(sprintf("The service '%s' for user provider for the export system", $config['export']['user_provider']));
            }
            $container->setAlias('parthenon.export.user_provider', $config['export']['user_provider']);
        }
    }

    /**
     * @throws \Exception
     */
    private function configureDoctrine(float|array|bool|int|string|null $bundles, XmlFileLoader $loader): void
    {
        if (isset($bundles['DoctrineBundle'])) {
            $loader->load('services/orm/export.xml');
        }
    }

    /**
     * @throws \Exception
     */
    private function configureMongoDb(float|int|bool|array|string|null $bundles, XmlFileLoader $loader): void
    {
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $loader->load('services/odm/export.xml');
        }
    }
}
