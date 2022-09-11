<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
        $enabled = $container->getParameter('parthenon_multi_tenancy_enabled');

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
