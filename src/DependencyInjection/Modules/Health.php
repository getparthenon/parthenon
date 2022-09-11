<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\DependencyInjection\Modules;

use Parthenon\Health\Checks\CheckInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Health implements ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $nodeBuilder): void
    {
    }

    public function handleDefaultParameters(ContainerBuilder $container): void
    {
        // TODO: Implement handleDefaultParameters() method.
    }

    public function handleConfiguration(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services/health.xml');
        $container->registerForAutoconfiguration(CheckInterface::class)->addTag('parthenon.health.check');

        $bundles = $container->getParameter('kernel.bundles');

        $this->configureDoctrine($bundles, $loader);
    }

    private function configureDoctrine(float|array|bool|int|string|null $bundles, XmlFileLoader $loader): void
    {
        if (isset($bundles['DoctrineBundle'])) {
            $loader->load('services/orm/health.xml');
        }
    }
}
