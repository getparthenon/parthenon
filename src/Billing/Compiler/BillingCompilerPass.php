<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
