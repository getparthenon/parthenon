<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Billing\Athena\CustomerTeamSection;
use Parthenon\Billing\Athena\CustomerUserSection;
use Parthenon\Billing\BillaBear\BillaBearPlanManager;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Plan\CachedPlanManager;
use Parthenon\Billing\Plan\PlanManager;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\TeamCustomerProvider;
use Parthenon\Billing\UserCustomerProvider;
use Parthenon\Billing\Webhook\HandlerInterface;
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
                ?->scalarNode('customer_type')->defaultValue('team')->end()
                ?->scalarNode('plan_management')->defaultValue('config')->end()
                ?->arrayNode('billabear')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('api_url')->end()
                        ->scalarNode('api_key')->end()
                    ->end()
                ->end()
                ?->arrayNode('payments')
                    ->children()
                        ->scalarNode('provider')->end()
                        ?->booleanNode('pci_mode')->end()
                        ?->scalarNode('return_url')->end()
                        ?->scalarNode('cancel_url')->end()
                        ?->arrayNode('adyen')
                            ->children()
                                ->scalarNode('api_key')->end()
                                ?->scalarNode('merchant_account')->end()
                                ?->booleanNode('test_mode')->end()
                                ?->scalarNode('webhook_secret')->end()
                                ?->scalarNode('prefix')->end()
                                ?->scalarNode('cse_url')->end()
                            ?->end()
                        ->end()
                        ?->arrayNode('stripe')
                            ->children()
                                ->scalarNode('private_api_key')->end()
                                ->scalarNode('webhook_secret')->end()
                                ?->scalarNode('public_api_key')->end()
                                ?->scalarNode('product_id')->end()
                                ?->arrayNode('payment_methods')
                                    ->scalarPrototype()->end()
                                ?->end()
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
        $container->setParameter('parthenon_billing_config_webhook_secret', '');
        $container->setParameter('parthenon_billing_plan_plans', []);
        $container->setParameter('parthenon_billing_product_id', null);
        $container->setParameter('parthenon_billing_billabear_enabled', false);
        $container->setParameter('parthenon_billing_billabear_api_url', false);
        $container->setParameter('parthenon_billing_billabear_api_key', false);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['billing']) || !isset($config['billing']['enabled']) || false === $config['billing']['enabled']) {
            return;
        }
        $container->registerForAutoconfiguration(HandlerInterface::class)->addTag('parthenon.billing.webhooks.handler');
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

        if (isset($billingConfig['billabear']) && $billingConfig['billabear']['enabled']) {
            $loader->load('services/billing/billabear.xml');
            $container->setAlias(PlanManagerInterface::class, BillaBearPlanManager::class);
            $this->handleBillaBearConfig($billingConfig['billabear'], $container);
        } elseif ('athena' === strtolower($billingConfig['plan_management'])) {
            $loader->load('services/billing/athena_plans.xml');
            $container->setAlias(PlanManagerInterface::class, CachedPlanManager::class);
        } else {
            $container->setAlias(PlanManagerInterface::class, PlanManager::class);
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

    public function buildPricesNode()
    {
        $treeBuilder = new TreeBuilder('prices');
        $node = $treeBuilder->getRootNode();

        $priceNode = $node->requiresAtLeastOneElement()
            ->useAttributeAsKey('payment_schedule')
            ->prototype('array');
        assert($priceNode instanceof ArrayNodeDefinition);

        $priceNode
            ->arrayPrototype()
                ->children()
                    ->scalarNode('amount')->end()
                    ->scalarNode('price_id')->end()
                    ->booleanNode('public')->defaultTrue()->end()
                ->end()
                ->end()
            ->end();

        return $node;
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

    protected function handleBillaBearConfig(array $billabearConfig, ContainerBuilder $containerBuilder): void
    {
        if (true !== $billabearConfig['enabled']) {
            return;
        }

        $containerBuilder->setParameter('parthenon_billing_billabear_enabled', true);
        $containerBuilder->setParameter('parthenon_billing_billabear_api_key', $billabearConfig['api_key']);
        $containerBuilder->setParameter('parthenon_billing_billabear_api_url', $billabearConfig['api_url']);
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

        $containerBuilder->setParameter('parthenon_billing_product_id', $paymentsConfig['stripe']['product_id'] ?? null);
        $containerBuilder->setParameter('parthenon_billing_config_frontend_info', $paymentsConfig['stripe']['public_api_key']);
        $containerBuilder->setParameter('parthenon_billing_config_webhook_secret', $paymentsConfig['stripe']['webhook_secret'] ?? '');

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

        $containerBuilder->setParameter('parthenon_billing_config_frontend_info', $paymentsConfig['adyen']['cse_url']);
        $containerBuilder->setParameter('parthenon_billing_config_webhook_secret', $paymentsConfig['adyen']['webhook_secret'] ?? '');

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
                    ?->booleanNode('is_per_seat')->defaultFalse()->end()
                    ?->booleanNode('public')->defaultTrue()->end()
                    ?->booleanNode('has_trial')->defaultFalse()->end()
                    ?->scalarNode('trial_length_days')->defaultValue(0)->end()
                    ?->scalarNode('user_count')->end()
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
            ->append($this->buildPricesNode())
            ->end();

        return $node;
    }
}
