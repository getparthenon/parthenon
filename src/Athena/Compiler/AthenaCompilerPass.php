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

namespace Parthenon\Athena\Compiler;

use Parthenon\Athena\AccessRightsManagerInterface;
use Parthenon\Athena\Crud\CrudController;
use Parthenon\Athena\Export\NormaliserFactoryInterface;
use Parthenon\Athena\Filters\FilterManager;
use Parthenon\Athena\ViewTypeManager;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class AthenaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('parthenon.athena.view_type_manager')) {
            return;
        }

        $this->handleSections($container);
        $this->handleDashboardSections($container);
        $this->handleController($container);
        $this->handleFilters($container);
        $this->handleViewTypes($container);
    }

    public function handleSectionNormalisers(ContainerBuilder $containerBuilder): void
    {
    }

    private function handleViewTypes(ContainerBuilder $container): void
    {
        $viewTypeManager = $container->getDefinition('parthenon.athena.view_type_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.athena.view_type');

        foreach ($definitions as $id => $tag) {
            $viewTypeManager->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function handleFilters(ContainerBuilder $container): void
    {
        $filterManager = $container->getDefinition('parthenon.athena.filter.filter_manager');
        $definitions = $container->findTaggedServiceIds('parthenon.athena.filter');

        foreach ($definitions as $name => $defintion) {
            $filterManager->addMethodCall('add', [new Reference($name)]);
        }
    }

    private function handleSections(ContainerBuilder $container): void
    {
        $definitions = $container->findTaggedServiceIds('parthenon.athena.section');
        /** @var Definition[] $sectionDefitions */
        $sectionDefitions = [];
        $classNames = [];
        foreach ($definitions as $id => $tag) {
            $defintion = $container->getDefinition($id);
            $className = $defintion->getClass();

            $sectionDefitions[$id] = $defintion;
            $classNames[$id] = $className;
        }

        foreach ($sectionDefitions as $id => $section) {
            if ($this->isExtended($section, $classNames)) {
                unset($sectionDefitions[$id]);
            }
        }

        $this->processSectionDefinitions($container, $sectionDefitions);
    }

    private function handleDashboardSections(ContainerBuilder $container): void
    {
        $definitions = $container->findTaggedServiceIds('parthenon.athena.dashboard_section');
        /** @var Definition[] $sectionDefitions */
        $sectionDefitions = [];
        $classNames = [];
        foreach ($definitions as $id => $tag) {
            $defintion = $container->getDefinition($id);
            $className = $defintion->getClass();

            $sectionDefitions[$id] = $defintion;
            $classNames[$id] = $className;
        }

        foreach ($sectionDefitions as $id => $section) {
            if ($this->isExtended($section, $classNames)) {
                unset($sectionDefitions[$id]);
            }
        }

        $dashboardSectionManagerDefinition = $container->getDefinition('parthenon.athena.dashboard_section_manager');
        foreach ($sectionDefitions as $id => $sectionDefition) {
            $dashboardSectionManagerDefinition->addMethodCall('add', [new Reference($id)]);
        }
    }

    private function isExtended(Definition $definition, array $classNames): bool
    {
        $className = $definition->getClass();

        $reflectionClass = new \ReflectionClass($className);

        if ($reflectionClass->isAbstract()) {
            return true;
        }

        foreach ($classNames as $name) {
            $parentName = get_parent_class($name);
            if ($parentName === $className) {
                return true;
            }
        }

        return false;
    }

    private function processSectionDefinitions(ContainerBuilder $container, array $definitions): void
    {
        $sectionManagerDefinition = $container->getDefinition('parthenon.athena.section_manager');
        foreach ($definitions as $name => $definition) {
            $className = $definition->getClass();

            $servicePart = $this->getServiceName($className);
            $sectionManagerDefinition->addMethodCall('addSection', [new Reference($name)]);

            $controllerDefinition = new Definition();
            $controllerDefinition->setClass(CrudController::class);
            $controllerDefinition->setArgument('$section', new Reference($name));
            $controllerDefinition->setArgument('$viewTypeManager', new Reference(ViewTypeManager::class));
            $controllerDefinition->setArgument('$filterManager', new Reference(FilterManager::class));
            $controllerDefinition->setArgument('$accessRightsManager', new Reference(AccessRightsManagerInterface::class));
            $controllerDefinition->setArgument('$security', new Reference(Security::class));
            $controllerDefinition->setPublic(true);
            $controllerDefinition->addTag('controller.service_arguments');
            $controllerDefinition->setAutoconfigured(true);
            $controllerDefinition->setAutowired(true);

            $container->setDefinition('athena_controller_'.$servicePart, $controllerDefinition);

            $normaliserDefinition = new Definition();
            $normaliserDefinition->setClass(NormaliserInterface::class);
            $normaliserDefinition->setFactory([new Reference(NormaliserFactoryInterface::class), 'build']);
            $normaliserDefinition->setArgument('$section', new Reference($name));
            $normaliserDefinition->addTag('parthenon.export.normaliser');

            $container->setDefinition('athena_export_normaliser_'.$servicePart, $normaliserDefinition);
        }
    }

    private function handleController(ContainerBuilder $container): void
    {
        $sectionManagerDefinition = $container->getDefinition('parthenon.athena.section_manager');

        $definitions = $container->findTaggedServiceIds('parthenon.athena.controller');

        foreach ($definitions as $id => $tag) {
            $sectionManagerDefinition->addMethodCall('addController', [new Reference($id)]);
            $defintion = $container->getDefinition($id);
            $defintion->addTag('controller.service_arguments');
        }
    }

    private function getServiceName(string $className): string
    {
        $parts = explode('\\', $className);
        $className = end($parts);

        return strtolower($className);
    }
}
