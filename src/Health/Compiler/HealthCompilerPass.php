<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HealthCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handleChecks($container);
    }

    private function handleChecks(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('parthenon.health.checks.check_manager')) {
            return;
        }

        $filterManager = $container->getDefinition('parthenon.health.checks.check_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.health.check');

        foreach ($definitions as $name => $defintion) {
            $filterManager->addMethodCall('addCheck', [new Reference($name)]);
        }
    }
}
