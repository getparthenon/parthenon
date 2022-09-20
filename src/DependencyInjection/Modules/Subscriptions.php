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

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\Subscriptions\Athena\TeamSubscriberSection;
use Parthenon\Subscriptions\Athena\UserSubscriberSection;
use Parthenon\Subscriptions\Plan\CounterInterface;
use Parthenon\Subscriptions\Repository\SubscriberRepositoryInterface;
use Parthenon\Subscriptions\Subscriber\SubscriberInterface;
use Parthenon\Subscriptions\Transition\ToActiveTransitionInterface;
use Parthenon\Subscriptions\Transition\ToCancelledTransitionInterface;
use Parthenon\Subscriptions\Transition\ToOverdueTransitionInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Subscriptions implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('subscriptions')
                ->children()
                    ->booleanNode('enabled')->end()
                    ->scalarNode('subscriber_type')->end()
                ->end()
                ->fixXmlConfig('plans')
                ->append($this->getPlansNode())
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_subscriptions_subscriber_type', '');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['subscriptions']) || !isset($config['subscriptions']['enabled']) || false == $config['subscriptions']['enabled']) {
            return;
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/subscriptions.xml');

        $container->setParameter('parthenon_subscriptions_plan_plans', $config['subscriptions']['plan']);
        $container->registerForAutoconfiguration(CounterInterface::class)->addTag('parthenon.subscriptions.plan.counter');
        $container->registerForAutoconfiguration(ToActiveTransitionInterface::class)->addTag('parthenon.subscriptions.transitions.to_active');
        $container->registerForAutoconfiguration(ToCancelledTransitionInterface::class)->addTag('parthenon.subscriptions.transitions.to_cancelled');
        $container->registerForAutoconfiguration(ToOverdueTransitionInterface::class)->addTag('parthenon.subscriptions.transitions.to_overdue');

        $this->configureSubscriberType($config, $container);
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureSubscriberType(array $config, ContainerBuilder $containerBuilder): array
    {
        if (isset($config['subscriptions']['subscriber_type'])) {
            if (SubscriberInterface::TYPE_USER == $config['subscriptions']['subscriber_type']) {
                $containerBuilder->setAlias(SubscriberRepositoryInterface::class, UserRepositoryInterface::class);
                // Remove TeamSubscriberSection so only UserSubscriberSection remains
                $containerBuilder->removeDefinition(TeamSubscriberSection::class);
            } elseif (SubscriberInterface::TYPE_TEAM == $config['subscriptions']['subscriber_type']) {
                $containerBuilder->setAlias(SubscriberRepositoryInterface::class, TeamRepositoryInterface::class);
                // Remove TeamSubscriberSection so only UserSubscriberSection remains
                $containerBuilder->removeDefinition(UserSubscriberSection::class);
            } else {
                throw new ParameterNotSetException('Invalid setting for subscriptions.subscriber_type');
            }

            $containerBuilder->setParameter('parthenon_subscriptions_subscriber_type', $config['subscriptions']['subscriber_type']);
        }

        return $config;
    }

    private function getPlansNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('plan');
        $node = $treeBuilder->getRootNode();

        /** @var ArrayNodeDefinition $planNode */
        $planNode = $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array');

        $planNode
            ->fixXmlConfig('limits')
                ->children()
                    ->booleanNode('is_free')->defaultFalse()->end()
                    ->booleanNode('is_per_seat')->defaultFalse()->end()
                    ->scalarNode('user_count')->end()
                    ->arrayNode('features')
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('limit')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->integerNode('limit')->end()
                            ->scalarNode('description')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
