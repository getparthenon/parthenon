<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Compiler;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RuleActionsCompilerPass extends AbstractCompilerPass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('parthenon.rule_engine.action.action_manager')) {
            return;
        }
        $this->handle($container, 'parthenon.rule_engine.action.action_manager', 'parthenon.rule_engine.action', 'addAction');
        $this->handleWrapping($container);
    }

    public function handleWrapping(ContainerBuilder $containerBuilder)
    {
        // Super weird workaround. If there is an attempt to fetch the definition using the id when using
        // tags to find tagged services it results in an error about constructing at build time from a parent service.
        $definitions = $containerBuilder->getDefinitions();
        $proxyFactoryDefinition = $containerBuilder->getDefinition('parthenon.rule_engine.repository.proxy_factory');

        foreach ($definitions as $id => $definition) {
            $tag = $definition->getTag('parthenon.rule_engine.repository');
            if (empty($tag)) {
                continue;
            }

            $className = $definition->getClass();

            $newDefinition = new Definition();
            $newDefinition->setClass($className);
            $newDefinition->setFactory([$proxyFactoryDefinition, 'build']);
            $newDefinition->setArguments([$definition]);
            $newDefinition->addTag('parthenon.rule_engine.repository');

            $containerBuilder->setDefinition($id.'_orig', $definition);
            $containerBuilder->setDefinition($id, $newDefinition);
        }
    }
}
