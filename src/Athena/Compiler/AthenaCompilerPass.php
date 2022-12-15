<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Compiler;

use Parthenon\Athena\AccessRightsManagerInterface;
use Parthenon\Athena\Crud\CrudController;
use Parthenon\Athena\Export\NormaliserFactoryInterface;
use Parthenon\Athena\Filters\FilterManager;
use Parthenon\Athena\ViewTypeManager;
use Parthenon\Export\Normaliser\NormaliserInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Security;

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
