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

use DocRaptor\Doc;
use Mpdf\Mpdf;
use Parthenon\Common\Exception\MissingDependencyException;
use Parthenon\Common\Exception\ParameterNotSetException;
use Parthenon\Common\RequestHandler\RequestHandlerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Common implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->arrayNode('common')
            ->children()
                ->scalarNode('site_url')->end()
                ->arrayNode('elasticsearch')
                    ->children()
                        ->arrayNode('hosts')->scalarPrototype()->end()->end()
                        ->scalarNode('connection_type')->end()
                        ->scalarNode('cloud_id')->end()
                        ->scalarNode('api_key')->end()
                        ->scalarNode('api_id')->end()
                        ->scalarNode('basic_username')->end()
                        ->scalarNode('basic_password')->end()
                    ->end()
                ->end()
                ->arrayNode('pdf')
                    ->children()
                        ->scalarNode('generator')->end()
                        ->arrayNode('mpdf')
                            ->children()
                                ->scalarNode('tmp_dir')->defaultValue('/tmp')->end()
                            ->end()
                        ->end()
                        ->arrayNode('docraptor')
                            ->children()
                                ->scalarNode('api_key')->end()
                            ->end()
                        ->end()
                        ->arrayNode('wkhtmltopdf')
                            ->children()
                                ->scalarNode('bin')->defaultValue('/usr/bin/wkhtmltopdf')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->fixXmlConfig('uploaders')
            ->append($this->getUploadersNode())
        ->end();
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        $container->setParameter('parthenon_common_site_url', 'http://localhost');
        $container->setParameter('parthenon_common_pdf_wkhtmltopdf_bin', '');
        $container->setParameter('parthenon_common_pdf_docraptor_api_key', '');
        $container->setParameter('parthenon_common_uploaders', []);
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(RequestHandlerInterface::class)->addTag('parthenon.common.request_handler');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/common.xml');

        if (!isset($config['common'])) {
            return;
        }

        $container->setParameter('parthenon_common_site_url', $config['common']['site_url'] ?? '');

        $config = $this->configureUploader($config, $container);
        $config = $this->configurePdf($config, $container, $loader);

        $this->configureElasticsearch($config, $container);
    }

    private function getUploadersNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('uploader');
        $node = $treeBuilder->getRootNode();

        /** @var ArrayNodeDefinition $uploaderNode */
        $uploaderNode = $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array');

        $uploaderNode
            ->children()
                ->scalarNode('provider')->end()
                ->scalarNode('naming_strategy')->end()
                ->scalarNode('url')->end()
                ->arrayNode('local')
                    ->children()
                        ->scalarNode('path')->end()
                    ->end()
                ->end()
                ->arrayNode('s3')
                    ->children()
                        ->scalarNode('key')->end()
                        ->scalarNode('secret')->end()
                        ->scalarNode('region')->end()
                        ->scalarNode('endpoint')->end()
                        ->scalarNode('bucket_name')->end()
                        ->scalarNode('version')->end()
                        ->scalarNode('visibility')->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function configureElasticsearch(array $config, ContainerBuilder $container): void
    {
        if (isset($config['common']['elasticsearch'])) {
            $elasticsearchConfig = $config['common']['elasticsearch'];

            $definition = $container->getDefinition('parthenon.common.elasticsearch.config');

            if (isset($elasticsearchConfig['connection_type'])) {
                $definition->addMethodCall('setConnectionType', [$elasticsearchConfig['connection_type']]);
            }

            if (isset($elasticsearchConfig['hosts'])) {
                $definition->addMethodCall('setHosts', [$elasticsearchConfig['hosts']]);
            }

            if (isset($elasticsearchConfig['api_id'])) {
                $definition->addMethodCall('setApiId', [$elasticsearchConfig['api_id']]);
            }

            if (isset($elasticsearchConfig['api_key'])) {
                $definition->addMethodCall('setApiKey', [$elasticsearchConfig['api_key']]);
            }

            if (isset($elasticsearchConfig['basic_username'])) {
                $definition->addMethodCall('setBasicUsername', [$elasticsearchConfig['basic_username']]);
            }

            if (isset($elasticsearchConfig['basic_password'])) {
                $definition->addMethodCall('setBasicPassword', [$elasticsearchConfig['basic_password']]);
            }
            $container->setDefinition('parthenon.common.elasticsearch.config', $definition);
        }
    }

    /**
     * @throws ParameterNotSetException
     */
    private function configurePdf(array $config, ContainerBuilder $container, XmlFileLoader $loader): array
    {
        if (isset($config['common']['pdf']['generator']) && 'docraptor' === $config['common']['pdf']['generator']) {
            if (!class_exists(Doc::class)) {
                throw new MissingDependencyException('To use docraptor you need to have the docraptor/docraptor package installed. Do composer require docraptor/docraptor.');
            }

            if (!isset($config['common']['pdf']['docraptor']) || !$config['common']['pdf']['docraptor']['api_key']) {
                throw new ParameterNotSetException('When pdf generator is docraptor you need to set parthenon.common.pdf.docraptor.api_key');
            }
            $container->setParameter('parthenon_common_pdf_docraptor_api_key', $config['common']['pdf']['docraptor']['api_key']);

            $loader->load('services/common/pdf/docraptor.xml');
        } elseif (isset($config['common']['pdf']['generator']) && 'mpdf' === $config['common']['pdf']['generator']) {
            if (!class_exists(Mpdf::class)) {
                throw new MissingDependencyException('To use mpdf you need to have the mpdf/mpdf package installed. Do composer require mpdf/mpdf.');
            }

            $container->setParameter('parthenon.common.pdf.mpdf.tmp_dir', $config['common']['pdf']['mpdf']['tmp_dir']);
            $loader->load('services/common/pdf/mpdf.xml');
        } elseif (isset($config['common']['pdf']['generator']) && 'wkhtmltopdf' === $config['common']['pdf']['generator']) {
            if (!class_exists(\Knp\Snappy\Pdf::class)) {
                throw new MissingDependencyException('To use wkhtmltopdf you need to have the knplabs/knp-snappy. Do composer require knplabs/knp-snappy.');
            }

            $container->setParameter('parthenon_common_pdf_wkhtmltopdf_bin', $config['common']['pdf']['wkhtmltopdf']['bin']);
            $loader->load('services/common/pdf/wkhtmltopdf.xml');
        }

        return $config;
    }

    private function configureUploader(array $config, ContainerBuilder $container): array
    {
        if (isset($config['common']['uploader']) && is_array($config['common']['uploader'])) {
            $container->setParameter('parthenon_common_uploaders', $config['common']['uploader']);
        }

        return $config;
    }
}
