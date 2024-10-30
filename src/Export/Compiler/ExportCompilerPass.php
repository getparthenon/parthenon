<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
