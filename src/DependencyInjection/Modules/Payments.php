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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Payments implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('payments')
                ->children()
                    ->booleanNode('enabled')->end()
                    ->scalarNode('provider')->end()
                    ->scalarNode('subscriber_type')->end()
                    ->scalarNode('success_redirect_route')->end()
                    ->scalarNode('cancel_checkout_redirect_route')->end()
                    ->arrayNode('stripe')
                        ->children()
                            ->scalarNode('private_api_key')->end()
                            ->scalarNode('public_api_key')->end()
                            ->scalarNode('success_url')->end()
                            ->scalarNode('cancel_url')->end()
                            ->scalarNode('return_url')->end()
                        ->end()
                    ->end()
                ->end()
                ->fixXmlConfig('prices')
                ->append($this->getPricesNode())
            ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_payments_stripe_private_api_key', '');
        $container->setParameter('parthenon_payments_stripe_public_api_key', '');
        $container->setParameter('parthenon_payments_stripe_success_url', '');
        $container->setParameter('parthenon_payments_stripe_cancel_url', '');
        $container->setParameter('parthenon_payments_stripe_return_url', '');
        $container->setParameter('parthenon_payments_prices', []);
        $container->setParameter('parthenon_payments_success_redirect_route', 'app_index');
        $container->setParameter('parthenon_payments_cancel_checkout_redirect_route', null);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['payments']) || !isset($config['payments']['enabled']) || false == $config['payments']['enabled']) {
            return;
        }

        if ('stripe' === strtolower($config['payments']['provider'])) {
            $this->handlePaymentsStripe($config, $container);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/payments.xml');
    }

    private function handlePaymentsStripe(array $config, ContainerBuilder $containerBuilder)
    {
        if (empty($config['payments']['stripe'])) {
            throw new ParameterNotSetException('When payment.provider is stripe then payments.stripe needs to be provided');
        }

        $stripeConfig = $config['payments']['stripe'];
        $stripeConfig = $this->configureStripePrivateApiKey($stripeConfig);
        $stripeConfig = $this->configureStripePublicKey($stripeConfig);
        $stripeConfig = $this->configureStripeSuccessUrl($stripeConfig);
        $stripeConfig = $this->configureCancelUrl($stripeConfig);

        $containerBuilder->setParameter('parthenon_payments_stripe_private_api_key', $stripeConfig['private_api_key'] ?? '');
        $containerBuilder->setParameter('parthenon_payments_stripe_public_api_key', $stripeConfig['public_api_key'] ?? '');
        $containerBuilder->setParameter('parthenon_payments_stripe_success_url', $stripeConfig['success_url'] ?? '');
        $containerBuilder->setParameter('parthenon_payments_stripe_cancel_url', $stripeConfig['cancel_url'] ?? '');
        $containerBuilder->setParameter('parthenon_payments_stripe_return_url', $stripeConfig['return_url'] ?? '');

        $config = $this->configurePrice($config, $containerBuilder);

        $this->configureSuccessRedirectRoute($config, $containerBuilder);
    }

    private function getPricesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('price');
        $node = $treeBuilder->getRootNode();

        /** @var ArrayNodeDefinition $planNode */
        $planNode = $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array');

        $planNode
            ->fixXmlConfig('prices')
                    ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('paymentSchedule')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('price')->end()
                                ->scalarNode('price_id')->end()
                                ->booleanNode('subscription')->end()
                                ->scalarNode('trial_day_length')->end()
                            ->end()
                        ->end()
                    ->end()
            ->end();

        return $node;
    }

    private function configureSuccessRedirectRoute(array $config, ContainerBuilder $containerBuilder): void
    {
        if (isset($config['payments']['success_redirect_route'])) {
            $containerBuilder->setParameter('parthenon_payments_success_redirect_route', $config['payments']['success_redirect_route']);
        }
        if (isset($config['payments']['cancel_checkout_redirect_route']) && !empty($config['payments']['cancel_checkout_redirect_route'])) {
            $containerBuilder->setParameter('parthenon_payments_cancel_checkout_redirect_route', $config['payments']['cancel_checkout_redirect_route']);
        } else {
            $defaultRoute = $containerBuilder->getParameter('parthenon_payments_success_redirect_route');
            $containerBuilder->setParameter('parthenon_payments_cancel_checkout_redirect_route', $defaultRoute);
        }
    }

    private function configurePrice(array $config, ContainerBuilder $containerBuilder): array
    {
        if (isset($config['payments']['price'])) {
            $containerBuilder->setParameter('parthenon_payments_prices', $config['payments']['price']);
        }

        return $config;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureStripePrivateApiKey(mixed $stripeConfig): mixed
    {
        if (!isset($stripeConfig['private_api_key'])) {
            throw new ParameterNotSetException('When payment.provide is stripe then payments.stripe.private_api_key needs to be provided');
        }

        return $stripeConfig;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureStripePublicKey(mixed $stripeConfig): mixed
    {
        if (!isset($stripeConfig['public_api_key'])) {
            throw new ParameterNotSetException('When payment.provide is stripe then payments.stripe.public_api_key needs to be provided');
        }

        return $stripeConfig;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureStripeSuccessUrl(mixed $stripeConfig): mixed
    {
        if (!isset($stripeConfig['success_url'])) {
            throw new ParameterNotSetException('When payment.provide is stripe then payments.stripe.success_url needs to be provided');
        }

        return $stripeConfig;
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configureCancelUrl(mixed $stripeConfig): mixed
    {
        if (!isset($stripeConfig['cancel_url'])) {
            throw new ParameterNotSetException('When payment.provide is stripe then payments.stripe.cancel_url needs to be provided');
        }

        return $stripeConfig;
    }
}
