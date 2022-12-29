<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
