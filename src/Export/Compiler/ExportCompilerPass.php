<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export\Compiler;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Parthenon\Export\Exporter\ExporterManager;
use Parthenon\Export\Normaliser\NormaliserManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ExportCompilerPass extends AbstractCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $this->handle($container, NormaliserManager::class, 'parthenon.export.normaliser', 'addNormaliser');
        $this->handle($container, ExporterManager::class, 'parthenon.export.exporter', 'addExporter');

        $this->handleDataProviders($container);
    }

    public function handleDataProviders(ContainerBuilder $container)
    {
        $definitions = $container->findTaggedServiceIds('parthenon.export.data_provider');

        foreach ($definitions as $id => $definitionData) {
            $definition = $container->getDefinition($id);
            $definition->setPublic(true);
            // Just to be safe, even though it should be the same object returned by reference, overwrite it.
            $container->setDefinition($id, $definition);
        }
    }
}
