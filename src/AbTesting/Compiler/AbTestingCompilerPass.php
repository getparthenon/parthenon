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

namespace Parthenon\AbTesting\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AbTestingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handleEnabledDecider($container);
    }

    private function handleEnabledDecider(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('parthenon.ab_testing.decider.enabled_decider.decider_manager')) {
            return;
        }

        $viewTypeManager = $container->getDefinition('parthenon.ab_testing.decider.enabled_decider.decider_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.ab_testing.decider');

        foreach ($definitions as $id => $tagInfo) {
            $viewTypeManager->addMethodCall('add', [new Reference($id)]);
        }
    }
}
