<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
