<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
