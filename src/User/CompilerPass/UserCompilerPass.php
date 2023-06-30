<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class UserCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('parthenon.user.security.user_checker_observer')) {
            return;
        }

        $this->handleVoters($container);
        $this->handleActions($container);
        $this->handleExporter($container);
        $this->handleExporterFormatter($container);
        $this->handleUserCheckers($container);
    }

    private function handleUserCheckers(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.user.security.user_checker_observer');
        $definitions = $container->findTaggedServiceIds('parthenon.user.security.user_checker');

        foreach ($definitions as $id => $tagInfo) {
            $filterManager->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function handleExporter(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.user.gdpr.export.exporter_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.user.gdpr.export.exporter');

        foreach ($definitions as $id => $tagInfo) {
            $filterManager->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function handleExporterFormatter(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.user.gdpr.export.formatter_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.user.gdpr.export.formatter');

        foreach ($definitions as $id => $tagInfo) {
            $filterManager->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function handleVoters(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.user.gdpr.deletion.decider');
        $definitions = $container->findTaggedServiceIds('parthenon.user.gdpr.delete.voter');

        foreach ($definitions as $id => $tagInfo) {
            $filterManager->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function handleActions(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.user.gdpr.deletion.deleter');
        $definitions = $container->findTaggedServiceIds('parthenon.user.gdpr.delete.deleter');

        foreach ($definitions as $id => $tagInfo) {
            $filterManager->addMethodCall('add', [new Reference($id)]);
        }
    }
}
