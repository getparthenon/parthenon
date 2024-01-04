<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Compiler;

use Parthenon\Billing\Webhook\HandlerManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BillingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handleEnabledDecider($container);
    }

    private function handleEnabledDecider(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(HandlerManager::class)) {
            return;
        }

        $handlerManager = $container->getDefinition(HandlerManager::class);
        $definitions = $container->findTaggedServiceIds('parthenon.billing.webhooks.handler');

        foreach ($definitions as $id => $tagInfo) {
            $handlerManager->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
