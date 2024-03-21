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

namespace Parthenon\MultiTenancy\Compiler;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\MultiTenancy\TenantProvider\TenantProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MultiTenancyCompilerPass extends AbstractCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $enabled = $container->getParameter('parthenon_multi_tenancy_is_enabled');

        if (!$enabled) {
            return;
        }

        $dbalConnection = $container->getParameter('parthenon_multi_tenancy_dbal_connection');
        $id = sprintf('doctrine.dbal.%s_connection', $dbalConnection);
        if (!$container->hasDefinition($id)) {
            throw new GeneralException(sprintf('There is no dbal connection called %s', $dbalConnection));
        }

        $definition = $container->getDefinition($id);
        $definition->addMethodCall('setCurrentTenantProvider', [new Reference(TenantProviderInterface::class)]);
        $container->setDefinition($id, $definition);

        $container->setAlias('parthenon.multi_tenancy.dbal.connection', $id);

        $globalDbalConnection = $container->getParameter('parthenon_multi_tenancy_global_dbal_connection');

        if (empty($globalDbalConnection)) {
            $globalDbalConnection = 'default';
        }

        $globalId = sprintf('doctrine.dbal.%s_connection', $globalDbalConnection);
        $container->setAlias('parthenon.multi_tenancy.dbal.global_connection', $globalId);
    }
}
