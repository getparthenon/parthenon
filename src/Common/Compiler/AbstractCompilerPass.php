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

namespace Parthenon\Common\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractCompilerPass implements CompilerPassInterface
{
    protected function handle(ContainerBuilder $container, string $managerDefinitionId, string $tagName, string $methodName): void
    {
        if (!$container->hasDefinition($managerDefinitionId)) {
            return;
        }

        $manager = $container->getDefinition($managerDefinitionId);
        $definitions = $container->findTaggedServiceIds($tagName);
        foreach ($definitions as $name => $defintion) {
            $manager->addMethodCall($methodName, [new Reference($name)]);
        }
    }
}
