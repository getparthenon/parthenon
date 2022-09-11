<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
