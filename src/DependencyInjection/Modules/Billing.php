<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Billing\Athena\CustomerTeamSection;
use Parthenon\Billing\Athena\CustomerUserSection;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\TeamCustomerProvider;
use Parthenon\Billing\UserCustomerProvider;
use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\User\Repository\TeamRepositoryInterface;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Billing implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->arrayNode('billing')
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->scalarNode('customer_type')->defaultValue('team')->end()
                ?->arrayNode('payments')
                    ->children()
                        ->scalarNode('provider')->end()
                        ->booleanNode('pci_mode')->end()
                        ->scalarNode('return_url')->end()
                        ->scalarNode('cancel_url')->end()
                        ?->arrayNode('adyen')
                            ->children()
                                ->scalarNode('api_key')->end()
                                ->scalarNode('merchant_account')->end()
                                ->booleanNode('test_mode')->end()
                                ->scalarNode('prefix')->end()
                                ->scalarNode('cse_url')->end()
                            ->end()
                        ->end()
                        ->arrayNode('stripe')
                            ->children()
                                ->scalarNode('private_api_key')->end()
                                ->scalarNode('public_api_key')->end()
                                ->arrayNode('payment_methods')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->end()
                ->fixXmlConfig('plans')
                ->append($this->getPlansNode())
            ?->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_billing_payments_obol_config', []);
        $container->setParameter('parthenon_billing_customer_type', 'team');
        $container->setParameter('parthenon_billing_config_frontend_info', '');
        $container->setParameter('parthenon_billing_plan_plans', []);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['billing']) || !isset($config['billing']['enabled']) || false === $config['billing']['enabled']) {
            return;
        }
        $container->setParameter('parthenon_billing_enabled', true);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/billing.xml');
        $loader->load('services/orm/billing.xml');

        $billingConfig = $config['billing'];
        $paymentsConfig = $billingConfig['payments'];

        if ('team' === strtolower($billingConfig['customer_type'])) {
            $this->handleTeamCustomer($config, $container);
        } elseif ('user' === strtolower($billingConfig['customer_type'])) {
            $this->handleUserCustomer($config, $container);
        }
        $container->setParameter('parthenon_billing_plan_plans', $config['billing']['plan']);

        $obolConfig = match ($paymentsConfig['provider']) {
            'stripe' => $this->handleStripeConfig($paymentsConfig, $container),
            'adyen' => $this->handleAdyen($paymentsConfig, $container),
            'custom' => [],
            default => throw new ParameterNotSetException('billing.payments.provider must be valid'),
        };

        $container->setParameter('parthenon_billing_payments_obol_config', $obolConfig);
        $container->setParameter('parthenon_billing_plan_plans', $config['billing']['plan']);
    }

    protected function handleTeamCustomer(array $config, ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->setAlias(CustomerProviderInterface::class, TeamCustomerProvider::class);
        $containerBuilder->setAlias(CustomerRepositoryInterface::class, TeamRepositoryInterface::class);
        $containerBuilder->removeDefinition(CustomerUserSection::class);
    }

    protected function handleUserCustomer(array $config, ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->setAlias(CustomerProviderInterface::class, UserCustomerProvider::class);
        $containerBuilder->setAlias(CustomerRepositoryInterface::class, UserRepositoryInterface::class);
        $containerBuilder->removeDefinition(CustomerTeamSection::class);
    }

    protected function handleStripeConfig(array $paymentsConfig, ContainerBuilder $containerBuilder): array
    {
        if (!isset($paymentsConfig['stripe']['private_api_key'])) {
            throw new ParameterNotSetException('billing.payments.stripe.private_api_key must be set.');
        }

        $pciMode = false;

        if (isset($paymentsConfig['pci_mode'])) {
            $pciMode = $paymentsConfig['pci_mode'];
        }

        $config = [
            'provider' => 'stripe',
            'api_key' => $paymentsConfig['stripe']['private_api_key'],
            'pci_mode' => $pciMode,
        ];

        $containerBuilder->setParameter('parthenon_billing_config_frontend_info', $paymentsConfig['stripe']['public_api_key']);

        if (isset($paymentsConfig['stripe']['payment_methods'])) {
            $config['payment_methods'] = $paymentsConfig['stripe']['payment_methods'];
        }

        if (isset($paymentsConfig['return_url'])) {
            $config['success_url'] = $paymentsConfig['return_url'];
            $config['cancel_url'] = $paymentsConfig['return_url'];
        }

        if (isset($paymentsConfig['cancel_url'])) {
            $config['cancel_url'] = $paymentsConfig['cancel_url'];
        }

        return $config;
    }

    protected function handleAdyen(array $paymentsConfig, ContainerBuilder $containerBuilder): array
    {
        if (!isset($paymentsConfig['adyen']['api_key'])) {
            throw new ParameterNotSetException('billing.payments.adyen.api_key must be set.');
        }
        if (!isset($paymentsConfig['adyen']['merchant_account'])) {
            throw new ParameterNotSetException('billing.payments.adyen.merchant_account must be set.');
        }

        $pciMode = false;
        if (isset($paymentsConfig['pci_mode'])) {
            $pciMode = $paymentsConfig['pci_mode'];
        }

        $testMode = true;
        if (isset($paymentsConfig['adyen']['test_mode'])) {
            $testMode = $paymentsConfig['adyen']['test_mode'];
        }

        $config = [
            'provider' => 'adyen',
            'api_key' => $paymentsConfig['adyen']['api_key'],
            'merchant_account' => $paymentsConfig['adyen']['merchant_account'],
            'pci_mode' => $pciMode,
            'test_mode' => $testMode,
        ];

        if ($paymentsConfig['adyen']['prefix']) {
            $config['prefix'] = $paymentsConfig['adyen']['prefix'];
        }

        if (isset($paymentsConfig['return_url'])) {
            $config['return_url'] = $paymentsConfig['return_url'];
        }

        $containerBuilder->setParameter('parthenon_billing_config_frontend_info', $paymentsConfig['stripe']['cse_url']);

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
