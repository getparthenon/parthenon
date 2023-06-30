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
