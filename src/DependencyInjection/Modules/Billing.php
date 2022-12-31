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

use Parthenon\Common\Exception\ParameterNotSetException;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
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
            ?->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_billing_payments_obol_config', []);
        $container->setParameter('parthenon_billing_customer_type', 'team');
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));

        if (!isset($config['billing']) || !isset($config['billing']['enabled']) || false === $config['billing']['enabled']) {
            return;
        }
        $billingConfig = $config['billing'];
        $paymentsConfig = $billingConfig['payments'];

        $obolConfig = match ($paymentsConfig['provider']) {
            'stripe' => $this->buildStripeObolConfig($paymentsConfig),
            'adyen' => $this->buildAdyenObolConfig($paymentsConfig),
            'custom' => [],
            default => throw new ParameterNotSetException('billing.payments.provider must be valid'),
        };

        $container->setParameter('parthenon_billing_payments_obol_config', $obolConfig);
    }

    protected function buildStripeObolConfig(array $paymentsConfig): array
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

    protected function buildAdyenObolConfig(array $paymentsConfig): array
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

        return $config;
    }
}
