<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
