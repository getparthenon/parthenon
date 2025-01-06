<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Funnel\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FunnelCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handleRepositories($container);
        $this->handleActions($container);
    }

    private function handleRepositories(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('parthenon.funnel.repository.repository_manager')) {
            return;
        }

        $filterManager = $container->getDefinition('parthenon.funnel.repository.repository_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.funnel.repository');

        foreach ($definitions as $name => $defintion) {
            $filterManager->addMethodCall('addRepository', [new Reference($name)]);
        }
    }

    private function handleActions(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('parthenon.funnel.unfinnished_actions.action_manager')) {
            return;
        }

        $filterManager = $container->getDefinition('parthenon.funnel.unfinnished_actions.action_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.funnel.action');

        foreach ($definitions as $name => $defintion) {
            $filterManager->addMethodCall('addAction', [new Reference($name)]);
        }
    }
}
