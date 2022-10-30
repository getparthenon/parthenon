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

use Parthenon\AbTesting\Decider\EnabledDeciderInterface;
use Parthenon\Common\Exception\ParameterNotSetException;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class AbTesting implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->arrayNode('ab_testing')
            ->children()
                ->booleanNode('enabled')->end()
                ->scalarNode('provider')->defaultValue('parthenon')->end()
                ->arrayNode('optimizely')
                    ->children()
                        ->scalarNode('api_key')->end()
                    ->end()
                ->end()
                ->arrayNode('parthenon')
                    ->children()
                        ->scalarNode('report_handler_service')->end()
                        ->scalarNode('dbal_connection_service')->end()
                        ->booleanNode('predefined_decisions_enabled')->end()
                        ->scalarNode('predefined_decisions_redis_service')->end()
                        ->booleanNode('log_user_results')->end()
                        ->arrayNode('disabled_user_agents')->scalarPrototype()->end()
                    ->end()
                ->end()
                ->end()
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon.ab_testing.disabled_user_agents', []);
        $container->setParameter('parthenon_abtesting_decsions_enabled', false);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['ab_testing']) || !isset($config['ab_testing']['enabled']) || false == $config['ab_testing']['enabled']) {
            return;
        }

        $container->registerForAutoconfiguration(EnabledDeciderInterface::class)->addTag('parthenon.ab_testing.decider');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));

        $config = $this->configureReports($config, $container);

        $config = $this->configureDisabledUserAgents($config, $container);

        $config = $this->configreDecisions($config, $container, $loader);

        $logUserResults = $config['ab_testing']['parthenon']['log_user_results'] ?? false;
        $container->setParameter('ab_testing_log_user_results', $logUserResults);

        $config = $this->configureOptimizely($config, $container, $loader);

        $this->configureDbal($config, $container, $loader);
        $loader->load('services/ab_testing.xml');
        $loader->load('services/ab_testing/parthenon.xml');
    }

    /**
     * @throws \Exception
     */
    private function configureDbal(array $config, ContainerBuilder $container, XmlFileLoader $loader): void
    {
        if (isset($config['ab_testing']['parthenon']['dbal_connection_service'])) {
            $container->setAlias('parthenon_ab_dbal_connection', $config['ab_testing']['parthenon']['dbal_connection_service']);

            $loader->load('services/ab_testing_dbal.xml');
        }
    }

    private function configureReports(array $config, ContainerBuilder $container): array
    {
        if (isset($config['ab_testing']['report_handler_service'])) {
            $definition = $container->getDefinition('parthenon.ab_testing.report.generator');
            $definition->addMethodCall('setGenerationHandler', [new Reference($config['ab_testing']['parthenon']['report_handler_service'])]);
        }

        return $config;
    }

    private function configureDisabledUserAgents(mixed $config, ContainerBuilder $container): mixed
    {
        if (isset($config['ab_testing']['disabled_user_agents'])) {
            $container->setParameter('parthenon.ab_testing.disabled_user_agents', $config['ab_testing']['parthenon']['disabled_user_agents']);
        }

        return $config;
    }

    /**
     * @throws \Exception
     */
    private function configreDecisions(mixed $config, ContainerBuilder $container, XmlFileLoader $loader): mixed
    {
        if (isset($config['ab_testing']['predefined_decisions_enabled']) && true === $config['ab_testing']['parthenon']['predefined_decisions_enabled']) {
            if (!isset($config['ab_testing']['predefined_decisions_redis_service'])) {
                throw new \Exception('Redis service is not defined');
            }

            $container->setParameter('parthenon_abtesting_decsions_enabled', true);
            $container->setAlias('parthenon.ab_testing.decider.choice_decider.cache_redis', $config['ab_testing']['parthenon']['predefined_decisions_redis_service']);
            $loader->load('services/ab_testing_decision_cache.xml');

            $definition = $container->getDefinition('parthenon.ab_testing.decider.choice_decider.decider_manager');
            $definition->addMethodCall('addDecider', [new Reference('parthenon.ab_testing.decider.choice_decider.predefined_choice')]);
        }

        return $config;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureOptimizely(mixed $config, ContainerBuilder $container, XmlFileLoader $loader): mixed
    {
        if (isset($config['ab_testing']['provider']) && 'optimizely' === strtolower($config['ab_testing']['provider'])) {
            if (!isset($config['ab_testing']['optimizely']['api_key'])) {
                throw new ParameterNotSetException('If parthenon.abtesting.provider is optimizely then the parthenon.ab_testing.optimizely.api_key must be set.');
            }
            $container->setParameter('parthenon_ab_testing_optimizely_api_key', $config['ab_testing']['optimizely']['api_key']);

            $loader->load('services/ab_testing/optimizely.xml');

            $loader->load('services/ab_testing.xml');
        }

        return $config;
    }
}
